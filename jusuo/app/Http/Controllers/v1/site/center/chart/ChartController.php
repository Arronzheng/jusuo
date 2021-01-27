<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2020/2/23
 * Time: 22:47
 */
namespace App\Http\Controllers\v1\site\center\chart;

use App\Http\Services\v1\admin\AuthService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\FavProduct;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChartController extends ApiController{

    private $authService;

    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //方案近7天浏览量
    public function album_visit_num(Request $request){
        $designer = $request->user();
        $album_ids = $designer->albums()->pluck('id')->toArray();

        $data = array();
        for($i=0;$i<7;$i++){
            $start_time = Carbon::today()->subDays($i);
            $end_time = Carbon::today()->subDays($i+1);
            $num = DB::table('visit_albums')->whereIn('album_id',$album_ids)->whereBetween('created_at',[$end_time,$start_time])->count();
            $data['date'][$i] = $end_time->toDateString();
            $data['num'][$i] = $num;
        }

        $start_time = Carbon::today();
        $end_time = Carbon::today()->subDays(1);
        $yes_num = $num = DB::table('visit_albums')->whereIn('album_id',$album_ids)->whereBetween('created_at',[$end_time,$start_time])->count();
        $data['yes_num'] = $yes_num;

        $startMonth = Carbon::today()->startOfMonth();
        $endMonth = Carbon::today()->endOfMonth();
        $month_num =  DB::table('visit_albums')->whereIn('album_id',$album_ids)->whereBetween('created_at',[$startMonth,$endMonth])->count();
        $data['month_num'] = $month_num;

        return $this->respDataReturn($data);
    }

    //方案7天收藏
    public function album_collect_num(Request $request){
        $designer = $request->user();
        $album_ids = $designer->albums()->pluck('id')->toArray();
        $data = array();
        for($i=0;$i<7;$i++){
            $start_time = Carbon::today()->subDays($i);
            $end_time = Carbon::today()->subDays($i+1);
            $num = DB::table('fav_albums')->whereIn('album_id',$album_ids)->whereBetween('created_at',[$end_time,$start_time])->count();
            $data['date'][$i] = $end_time->toDateString();
            $data['num'][$i] = $num;
        }

        $start_time = Carbon::today();
        $end_time = Carbon::today()->subDays(1);
        $yes_num = $num = DB::table('fav_albums')->whereIn('album_id',$album_ids)->whereBetween('created_at',[$end_time,$start_time])->count();
        $data['yes_num'] = $yes_num;

        $startMonth = Carbon::today()->startOfMonth();
        $endMonth = Carbon::today()->endOfMonth();
        $month_num =  DB::table('fav_albums')->whereIn('album_id',$album_ids)->whereBetween('created_at',[$startMonth,$endMonth])->count();
        $data['month_num'] = $month_num;

        return $this->respDataReturn($data);

    }

    public function album_top(Request $request){
        $designer = $request->user();

        $visit_top = Album::where('designer_id',$designer->id)
            ->join('search_albums as sa','sa.album_id','=','albums.id')
            ->where('visible_status',Album::VISIBLE_STATUS_ON)
            ->orderBy('count_visit','desc')
            ->limit(5)
            ->select(['albums.title','albums.photo_cover','albums.count_visit','albums.count_fav','sa.web_id_code'])
            ->get();
        $visit_top->transform(function($v){
            if(!Str::startsWith($v->count_fav,['http://','https://'])){
                $v->photo_cover = url($v->photo_cover);
            }
            return $v;
        });

        $collect_top = $designer->albums()
            ->where('visible_status',Album::VISIBLE_STATUS_ON)
            ->join('search_albums as sa','sa.album_id','=','albums.id')
            ->orderBy('count_fav','desc')
            ->limit(5)
            ->select(['albums.title','albums.photo_cover','albums.count_visit','albums.count_fav','sa.web_id_code'])
            ->with(['searchAlbums'])
            ->get();
        $collect_top->transform(function($v){
            if(!Str::startsWith($v->count_fav,['http://','https://'])){
                $v->photo_cover = url($v->photo_cover);
            }
            return $v;
        });
        $data['visit'] = $visit_top;
        $data['collect'] = $collect_top;

        return $this->respDataReturn($data);
    }

    public function product_user_num(Request $request){
        $designer = $request->user();
        $start_time = Carbon::today();
        $end_time=  Carbon::today()->subDays(7);
        $album_ids = $designer->albums()->whereBetween('created_at',[$end_time,$start_time])->pluck('id')->toArray();

        $product_ids = DB::table('album_product_ceramics')->whereIn('album_id',$album_ids)->pluck('product_ceramic_id')->toArray();

        $total = count($product_ids);
        $sign_product_ids = array_unique($product_ids);
        $arr = array();
        foreach ($sign_product_ids as $k => $id){
            $arr[$k]['id'] = $id;
            $arr[$k]['num'] = 0;
            foreach ($product_ids as $v){
                if($id == $v){
                    $arr[$k]['num']++;
                }
            }
        }
        $data = array();
        $data['name'] = array();
        $data['time'] = array();
        foreach ($arr as $v){
            $product_title = ProductCeramic::where('id',$v['id'])->value('name');
            array_push($data['name'],$product_title);
            $time = $v['num'] / $total * 100;
            array_push($data['time'],$time);
        }
        return $this->respDataReturn($data);

    }

    public function product_top(Request $request){
        $designer = $request->user();

        $builder = ProductCeramic::query()
            ->with(['brand'=>function($query){
                $query->select(['id','short_name']);
            }])
            ->select(['id','brand_id','web_id_code','photo_product','guide_price','name as productTitle','count_fav','count_visit','status','spec_id'])
            ->where('visible',ProductCeramic::VISIBLE_YES);

        if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            $orgId = $designer->organization_id;
            $builder->where('brand_id',$orgId);
        }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){

            $seller_id = $designer->organization_id;
            $seller = OrganizationDealer::where('id',$seller_id)->first();
            if($seller->p_dealer_id == 0){
                $product_ids =DB::table('product_ceramic_authorizations')
                    ->where('dealer_id',$seller->id)
                    ->where('status','!=',ProductCeramicAuthorization::statusGroup(ProductCeramicAuthorization::STATUS_OFF))
                    ->pluck('product_id')->toArray();
            }else{
                $p_seller = OrganizationDealer::where('id',$seller->p_dealer_id)->first();
                if($p_seller){
                    $product_ids =DB::table('product_ceramic_authorizations')
                        ->where('dealer_id',$p_seller->id)
                        ->where('status','!=',ProductCeramicAuthorization::statusGroup(ProductCeramicAuthorization::STATUS_OFF))
                        ->pluck('product_id')->toArray();
                }
            }
            $builder->whereIn('product_ceramics.id',$product_ids);

        }else{
            return $this->respDataReturn([],'用户无所属组织');
        }

        $visit = $builder->orderBy('count_visit','desc')->limit(5)->get();
        $visit->transform(function($v) use($designer){

            $v->collected = false;
            if($designer){
                $collected = FavProduct::where('designer_id',$designer->id)->where('product_id',$v->id)->first();
                if($collected){ $v->collected = true; }
            }

            $v->cover =  '';
            $photo_product = \Opis\Closure\unserialize($v->photo_product);
            if(isset($photo_product[0])){
                $v->cover = $photo_product[0];
            }
            return $v;
        });

        $collect = $builder->orderBy('count_fav','desc')->limit(5)->get();
        $collect->transform(function($v) use($designer){

            $v->collected = false;
            if($designer){
                $collected = FavProduct::where('designer_id',$designer->id)->where('product_id',$v->id)->first();
                if($collected){ $v->collected = true; }
            }

            $v->cover =  '';
            $photo_product = \Opis\Closure\unserialize($v->photo_product);
            if(isset($photo_product[0])){
                $v->cover = $photo_product[0];
            }
            return $v;
        });

        $data['visit'] = $visit;
        $data['collect'] = $collect;


        return $this->respDataReturn($data);
    }

}