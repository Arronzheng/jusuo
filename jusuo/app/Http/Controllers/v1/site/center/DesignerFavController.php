<?php

namespace App\Http\Controllers\v1\site\center;

use App\Http\Controllers\v1\VersionController;
use App\Models\Album;
use App\Models\Area;
use App\Models\Designer;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use App\Models\FavProduct;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramicAuthorization;
use App\Models\StatisticDesigner;
use App\Models\ProductCeramic;
use App\Services\v1\site\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class DesignerFavController extends VersionController
{
    //
    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function fav_albums(Request $request){

        $user = $request->user();

        $albums = $user->fav_albums()->where('status',Album::STATUS_PASS)->where('visible_status',100)->with('designerDetail','searchAlbums')->get();

        $albums->transform(function($v) use ($user){

            $v->fav = false;
            if($user){
                $fav = FavAlbum::where('designer_id',$user->id)->where('album_id',$v->id)->first();
                if($fav){$v->fav=true;}
            }

            $v->liked = false;
            if($user){
                $liked = FavAlbum::where('designer_id',$user->id)->where('album_id',$v->id)->first();
                if($liked){ $v->liked = true;}
            }

            if(!Str::startsWith($v->photo_cover,['http://','https://'])){
                $v->photo_cover = url($v->photo_cover);
            }

            if(!Str::startsWith( $v->designerDetail->url_avatar,['http://','https://'])){
                $v->designerDetail->url_avatar = url($v->designerDetail->url_avatar);
            }

            //是否全景图
            $v->panorama = $v->type==Album::TYPE_KUJIALE_SOURCE?true:false;

            return $v;
        });


        return $this->apiSv->respDataReturn($albums);
    }

    public function  get_fav_designer(Request $request){
        $user = $request->user();

        $designers = $user->fav_designers()->where('status',Designer::STATUS_ON)->with('detail')->get();

        $designers->transform(function($v) use($user){

            $v->focused = false;
            if($user){
                $focused = FavDesigner::where('target_designer_id',$v->id)->where('designer_id',$user->id)->first();
                if($focused){ $v->focused = true; }
            }

            //擅长风格
            $v->styles_text = '';
            $styles = $v->styles()->get()->pluck('name')->toArray();
            if(is_array($styles) && count($styles)>0){
                $v->styles_text = implode(',',$styles);
            }

            //粉丝数、设计方案数
            $v->fans = 0;
            $v->count_upload_album = 0;
            $stat = StatisticDesigner::where('designer_id',$v->id)->orderBy('id','desc')->first();
            if($stat){
                $v->fans = intval($stat->count_faved_designer);
                $v->count_upload_album = intval($stat->count_upload_album);
            }

            //3个最新设计方案封面、标题
            $v->limit_albums = array();
            $albums = Album::query()
                ->where('designer_id',$v->id)
                ->where('status',Album::STATUS_PASS)
                ->join('search_albums as sa','sa.album_id','=','albums.id')
                ->limit(2)
                ->orderBy('albums.id','desc')
                ->select(['albums.title as name','albums.photo_cover','sa.web_id_code'])
                ->get();
            if(count($albums) >0){
                foreach ($albums as $album){
                    if(!Str::startsWith($album->photo_cover,['http://','https://'])){
                        $album->photo_cover = url($album->photo_cover);
                    }

                }
                $v->limit_albums = $albums;
            }

            //等级
            $v->level = Designer::designerTitleCn($v->detail->self_designer_level);

            //服务地区
            $v->area_text = '';
            $province =  Area::where('id',$v->detail->area_serving_province)->first();
            $city =  Area::where('id',$v->detail->area_serving_city)->first();
            if($province){$v->area_text.= $province->name;}
            if($city){$v->area_text.= $city->name;}

            if(!Str::startsWith($v->detail->url_avatar,['http://','https://'])){
                $v->detail->url_avatar = url($v->detail->url_avatar);
            }

            return $v;
        });


        return $this->apiSv->respDataReturn($designers);
    }

    public function  get_fav_product(Request $request){
        $designer = $request->user();


        $data = $designer->fav_products()->where('status',ProductCeramic::STATUS_PASS)->with('dealer','brand');

//        if($org = $request->input('org','')){
//            if($org == 'org'){
//
//                if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
//                    //如果属于品牌
//                    $orgId = $designer->organization_id;
//                    $data->where('product_ceramics.brand_id',$orgId);
//
//                }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
//                    //属于经销商
//                    $seller_id = $designer->organization_id;
//                    $seller = OrganizationDealer::where('id',$seller_id)->first();
//                    if($seller->p_dealer_id == 0){
//                        //是否一级
//                        $product_ids =DB::table('product_ceramic_authorizations')
//                            ->where('dealer_id',$seller->id)
//                            ->where('status','!=',ProductCeramicAuthorization::statusGroup(ProductCeramicAuthorization::STATUS_OFF))
//                            ->pluck('product_id')->toArray();
//                    }else{
//                        //二级 找到上一级
//                        $p_seller = OrganizationDealer::where('id',$seller->p_dealer_id)->first();
//                        if($p_seller){
//                            $product_ids =DB::table('product_ceramic_authorizations')
//                                ->where('dealer_id',$p_seller->id)
//                                ->where('status','!=',ProductCeramicAuthorization::statusGroup(ProductCeramicAuthorization::STATUS_OFF))
//                                ->pluck('product_id')->toArray();
//                        }
//                    }
//                    $data->whereIn('product_ceramics.id',$product_ids);
//                }else{
//
//                }
//            }else if($org == 'other'){
//
//                if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
//                    //如果属于品牌
//                    $orgId = $designer->organization_id;
//                    $data->where('product_ceramics.brand_id','!=',$orgId);
//
//                }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
//                    //属于经销商
//                    $seller_id = $designer->organization_id;
//                    $seller = OrganizationDealer::where('id',$seller_id)->first();
//                    if($seller->p_dealer_id == 0){
//                        //是否一级
//                        $product_ids =DB::table('product_ceramic_authorizations')
//                            ->where('dealer_id','!=',$seller->id)
//                            ->where('status','!=',ProductCeramicAuthorization::statusGroup(ProductCeramicAuthorization::STATUS_OFF))
//                            ->pluck('product_id')->toArray();
//                    }else{
//                        //二级 找到上一级
//                        $p_seller = OrganizationDealer::where('id',$seller->p_dealer_id)->first();
//                        if($p_seller){
//                            $product_ids =DB::table('product_ceramic_authorizations')
//                                ->where('dealer_id','!=',$p_seller->id)
//                                ->where('status','!=',ProductCeramicAuthorization::statusGroup(ProductCeramicAuthorization::STATUS_OFF))
//                                ->pluck('product_id')->toArray();
//                        }
//                    }
//                    $data->whereIn('product_ceramics.id',$product_ids);
//                }else{
//
//                }
//            }
//        }

        $products = $data->get();

        $products->transform(function($v) use($designer){

            $v->collected = false;
            if($designer){
                $collected = FavProduct::where('designer_id',$designer->id)->where('product_id',$v->id)->first();
                if($collected){ $v->collected = true; }
            }

            //获取第一张产品图为封面
            $v->cover =  '';
            $photo_product = \Opis\Closure\unserialize($v->photo_product);
            if(isset($photo_product[0])){
                if(Str::startsWith($photo_product[0],['http://','https://'])){
                    $v->cover = $photo_product[0];
                }else {
                    $v->cover = url($photo_product[0]);
                }
            }


            return $v;
        });

        return $this->apiSv->respDataReturn($products);
    }


}
