<?php

namespace App\Http\Controllers\v1\site\designer\api;

use App\Http\Services\common\GlobalService;
use App\Http\Services\common\OrganizationService;

use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\Area;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\FavDesigner;
use App\Models\HouseType;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\ProductQa;
use App\Models\Space;
use App\Models\SpaceType;
use App\Models\StatisticDesigner;
use App\Models\StatisticProductCeramic;
use App\Models\Style;
use App\Services\v1\admin\OrganizationBrandService;
use App\Services\v1\site\BsDesignerDataService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class DesignerController extends ApiController
{

    public function __construct(

    ){

    }

    /*----------设计师首页相关------------*/

    //获取筛选类型数据
    public function list_filter_types(Request $request)
    {
        $query = $request->input('query','');
        $query_string = urldecode($query);
        parse_str($query_string,$query_array);

        $result = array();

        $styles = Style::select(['id','name'])->get();

        $spaces = Space::select(['id','name'])->get();

        $levels = DesignerDetail::designerLevelIdNameGroup();



        $style_info = array();
        $style_info['title'] = '擅长风格';
        $style_info['value'] = 'stl';
        $style_info['has_selected'] = false;
        if(isset($query_array[$style_info['value']])){
            $type_query = $query_array[$style_info['value']];
            for($i=0;$i<count($styles);$i++){
                if($styles[$i]->id == $type_query){
                    $styles[$i]->selected = 1;
                    $style_info['has_selected'] = true;
                }else{
                    $styles[$i]->selected = 0;
                }
            }
        }
        $style_info['data'] = $styles;


        $spaces_info = array();
        $spaces_info['title'] = '擅长空间';
        $spaces_info['value'] = 'sp';
        $spaces_info['has_selected'] = false;
        if(isset($query_array[$spaces_info['value']]) && $query_array[$spaces_info['value']]){
            $type_query = $query_array[$spaces_info['value']];
            for($i=0;$i<count($spaces);$i++){
                if($spaces[$i]->id == $type_query){
                    $spaces[$i]->selected = 1;
                    $spaces_info['has_selected'] = true;
                }else{
                    $spaces[$i]->selected = 0;
                }
            }
        }
        $spaces_info['data']= $spaces;

        $level_info = array();
        $level_info['title'] = '经验等级';
        $level_info['value'] = 'lv';
        $level_info['has_selected'] = false;
        if(isset($query_array[$level_info['value']]) && $query_array[$level_info['value']]){
            $type_query = $query_array[$level_info['value']];
            for($i=0;$i<count($levels);$i++){
                if($levels[$i]['id'] == $type_query){
                    $levels[$i]['selected'] = 1;
                    $level_info['has_selected'] = true;
                }else{
                    $levels[$i]['selected'] = 0;
                }
            }
        }
        $level_info['data']= $levels;

        $result[] = $style_info;
        $result[] = $spaces_info;
        $result[] = $level_info;

        return $this->respDataReturn($result);

    }

    //获取设计师列表数据
    public function list_designers(Request $request)
    {

        $designer = Auth()->user();

        $datas = BsDesignerDataService::listDesignerIndexData([
            'loginDesigner' => $designer,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id')
        ],$request);

        return $this->respDataReturn($datas);


    }

    //关注操作
    public function focus(Request $request){

        $designer = Auth()->user();

        $designer_code = $request->input('aid',0);
        $operation = $request->input('op',1);  //1关注2取消关注

        $operation = intval($operation);
        if(!in_array($operation,[1,2])){
            return $this->respFailReturn('操作错误');
        }

        if(!$designer_code){
            return $this->respFailReturn('参数错误');
        }

        $target_designer = Designer::where('web_id_code',$designer_code)->first();

        if(!$target_designer){
            return $this->respFailReturn('信息不存在');
        }

        if($target_designer->id == $designer->id){
            return $this->respFailReturn('不能关注自己哦');
        }

        $result = OpService::favDesigner($target_designer->id);

        if($result['result'] <0){
            return $this->respFailReturn($result['msg']);
        }else{
            return $this->respDataReturn([],$result['msg']);
        }


    }

    //获取优秀设计师
    public function list_nice_designers(Request $request)
    {
        $designer = Auth::user();

        $datas = BsDesignerDataService::listDesignerIndexNiceData([
            'loginDesigner' => $designer,
            'loginBrandId' => session('designer_scope.brand_id')
        ],$request);

        return $this->respDataReturn($datas);

    }

    //获取列表轮播图
    public function get_banner(Request $request){

        $banner = [];

        $pageBelongBrandId = session()->get('pageBelongBrandId');
        $loginDesigner = Auth::user();

        if(!isset($pageBelongBrandId) || !$pageBelongBrandId){
            //获取登录设计师所属品牌
            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                $pageBelongBrandId = $loginDesigner->organization_id;
            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                $loginDealer = OrganizationDealer::find($loginDesigner->organization_id);
                $pageBelongBrandId = $loginDealer->p_brand_id;
            }
        }

        $builder = Banner::query()
            ->where('status',Banner::STATUS_ON)
            ->where('position',Banner::POSITION_DESIGNER_INDEX_TOP)
            ->orderBy('sort','desc')
            ->orderBy('id','desc');

        if($pageBelongBrandId){
            $builder->where('brand_id',$pageBelongBrandId);
        }else{
            //$builder->where('brand_id',0);
            return $this->respFailReturn([]);
        }

        $banners = $builder->get();

        if($banners){
            foreach($banners as $item){
                $temp = [];
                $temp['image'] = $item->photo;
                $temp['url'] = $item->url;
                array_push($banner,$temp);
            }
        }else{
            return $this->respFailReturn('');
        }

        return $this->respDataReturn($banner);
    }


    /*----------设计师详情页相关------------*/

    //获取设计师基本信息
    public function get_designer_info($id,Request $request)
    {
        $designer = Auth::user();

        $data = Designer::query()
            ->has('detail')
            ->select(['id','web_id_code','status'])
            ->with(['detail'=>function($query){
                $query->select([
                    'designer_id','url_avatar','nickname','approve_realname','area_serving_province','area_serving_city',
                    'area_serving_district','self_working_year','point_focus','self_organization',
                    'self_education','self_introduction','self_award',
                ]);
            }])
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->respFailReturn('设计师不存在');
        }


        if(
            $data->status != Designer::STATUS_ON
        ){
            return $this->respFailReturn('设计师状态异常');
        }

        //擅长风格
        $data->styles_text = '';
        $styles = $data->styles()->get()->pluck('name')->toArray();
        if(is_array($styles) && count($styles)>0){
            $data->styles_text = implode(',',$styles);
        }

        //擅长空间
        $data->spaces_text = '';
        $spaces = $data->spaces()->get()->pluck('name')->toArray();
        if(is_array($spaces) && count($spaces)>0){
            $data->spaces_text = implode(',',$spaces);
        }

        //设计方案数
        $data->count_upload_album = 0;
        $stat = StatisticDesigner::where('designer_id',$data->id)->orderBy('id','desc')->first();
        if($stat){
            $data->count_upload_album = intval($stat->count_upload_album);
        }

        $data->focused = false;
        if($designer){
            $focused = FavDesigner::where('target_designer_id',$data->id)->where('designer_id',$designer->id)->first();
            if($focused){ $data->focused = true; }
        }

        //所在城市
        $province = Area::find($data->area_serving_province);
        $city = Area::find($data->area_serving_city);
        $area_text = $province?$province->short_name:''.$city?$city->short_name:'';
        $data->area_text = $area_text;


        unset($data->id);
        unset($data->status);
        unset($data->detail->id);

        return $this->respDataReturn($data);

    }

    //获取优秀
    public function list_product_accessories($id)
    {
        $data = ProductCeramic::query()
            ->where('sys_code',$id)
            ->first();


        if(!$data){
            return $this->respFailReturn('设计师不存在');
        }

        if(
            $data->status != ProductCeramic::STATUS_PASS
        ){
            return $this->respFailReturn('设计师状态异常');
        }


        $accessories = $data->accessories()->get();

        $accessories->transform(function($v){
            $temp = new \stdClass();

            $temp->photo = \Opis\Closure\unserialize($v->photo);
            $temp->code = $v->code;
            $temp->technology = $v->technology;
            //规格
            $temp->spec_text = $v->spec_width."x".$v->spec_length."mm";

            return $temp;
        });

        return $this->respDataReturn($accessories);

    }


    /*------------------废弃方法留存-------------------*/
    //获取设计师列表数据
    public function list_designers_old(Request $request)
    {

        $designer = Auth()->user();

        $builder = Designer::query()
            ->join('designer_details as detail','detail.designer_id','=','designers.id')
            //->join('statistic_designers as sd','sd.designer_id','=','designers.id')
            ->select([
                'designers.id','designers.web_id_code',
                DB::raw('( select count_upload_album from statistic_designers as sd
                        where sd.designer_id = designers.id order by sd.id desc limit 1) as  count_upload_album'),
                'detail.designer_id','detail.self_designer_level','detail.url_avatar','detail.nickname',
                'detail.area_serving_province','detail.area_serving_city','detail.point_focus',
                'detail.count_visit','detail.count_fav'
            ])
            ->where('designers.status',Designer::STATUS_ON);

        //是否品牌主页进
        if($brand_scope = $request->input('__bs',null)){
            $brand = OrganizationBrand::where('web_id_code',$brand_scope)->first();
            $brand_id = $brand->id;
            if($brand){
                if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                    //品牌设计师可见性
                    //所有品牌设计师+旗下销售商的所有设计师
                    //(只显示已有审核通过方案(album->status)的品牌设计师和已有品牌站可用方案(album->status_brand)的销售商设计师)
                    $builder->where(function($query1) use($brand_id){
                        //品牌设计师
                        $query1->where(function($brand_designer)use($brand_id){
                            $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                            $brand_designer->where('organization_id',$brand_id);
                            $brand_designer->whereHas('albums',function($album){
                                //品牌设计师筛选审核通过的方案
                                $album->where('status',Album::STATUS_PASS);
                            });
                        });
                        //旗下销售商的所有设计师
                        $query1->orWhere(function($seller_designer)use($brand_id){
                            $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                            $seller_designer->whereHas('seller',function($organization)use($brand_id){
                                $organization->where('organization_dealers.p_brand_id',$brand_id);
                            });
                            $seller_designer->whereHas('album',function($album)use($brand_id){
                                $album->where('status',Album::STATUS_PASS);
                                $album->where('status_brand',$brand_id);
                            });
                        });
                    });
                }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                    //销售商直属设计师可见性

                }else{
                    $builder->where('id',0);
                }

            }else{
                $builder->where('id',0);
            }


        }else{
            /*$builder->whereHas('album',function($query){
                $query->where('status_platform',Album::STATUS_PLATFORM_ON);
            });*/
            $builder->where('id',0);
        }

        //搜索名称/型号
        if($search = $request->input('search','')){
            $like = '%'.$search.'%';

            $builder->where(function($query) use ($like){
                $query->where('name','like',$like);
                $query->orWhere('code','like',$like);
            });
        }

        //是否擅长风格
        $style_id = $request->input('stl',null);
        if($style_id){
            $builder->whereHas('styles',function($query) use ($style_id){
                $query->where('styles.id',$style_id);

            });
        }

        //是否擅长空间
        $space_id = $request->input('sp',null);
        if($space_id){
            $builder->whereHas('spaces',function($query) use ($space_id){
                $query->where('spaces.id',$space_id);

            });
        }

        //是否筛选等级
        $level = $request->input('lv',null);
        if($level){
            $builder->whereHas('detail',function($query) use ($level){
                $query->where('self_designer_level',$level);
            });
        }

        //是否提交关键字
        if($keyword = $request->input('k',null)){
            $builder->whereRaw('(detail.nickname like "%'.$keyword.'%")');
        }


        //排序
        if($order = $request->input('order','')){
            if(preg_match('/^(.+)_(asc|desc)$/',$order,$m)){
                if(in_array($m[1],['comples','pop','album'])){
                    if($m[1] == 'comples'){
                        $builder->orderBy('point_focus',$m[2]);
                    }else if($m[1] == 'pop'){
                        $builder->orderBy('count_visit',$m[2])->orderBy('count_fav',$m[2]);
                    }else if($m[1] == 'album'){
                        $builder->orderBy('count_upload_album',$m[2]);
                    }
                }else{
                    $builder->orderBy('point_focus','desc');
                }
            }
        }

        $datas = $builder->paginate(12);

        $datas->transform(function($v) use($designer){

            $v->focused = false;
            if($designer){
                $focused = FavDesigner::where('target_designer_id',$v->id)->where('designer_id',$designer->id)->first();
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
            $v->albums = [];
            $albums = Album::query()
                ->where('designer_id',$v->id)
                ->where('status',Album::STATUS_PASS)
                ->limit(3)
                ->orderBy('id','desc')
                ->select(['title','photo_cover'])
                ->get();
            $v->albums = $albums;

            //等级
            $v->level = Designer::designerTitle($v->self_designer_level);

            //服务地区
            $v->area_text = '';
            $province =  Area::where('id',$v->area_serving_province)->first();
            $city =  Area::where('id',$v->area_serving_city)->first();
            if($province){$v->area_text.= $province->name;}
            if($city){$v->area_text.= $city->name;}

            unset($v->id);
            unset($v->designer_id);

            return $v;
        });


        return $this->respDataReturn($datas);


    }

    //获取优秀设计师
    public function list_nice_designers_old(Request $request)
    {
        $designer = Auth::user();

        $builder = Designer::query()
            ->has('detail')
            ->with('detail')
            ->where('status',Designer::STATUS_ON)
            ->where('top_brand_status',Designer::TOP_BRAND_STATUS_ON)
            ->limit(5)
            ->inRandomOrder();

        //是否品牌主页进
        if($brand_scope = $request->input('__bs',null)){
            $brand = OrganizationBrand::where('web_id_code',$brand_scope)->first();
            $brand_id = $brand->id;
            if($brand){
                if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                    //品牌设计师可见性
                    //所有品牌设计师+旗下销售商的所有设计师
                    //(只显示已有审核通过方案(album->status)的品牌设计师和已有品牌站可用方案(album->status_brand)的销售商设计师)
                    $builder->where(function($query1) use($brand_id){
                        //品牌设计师
                        $query1->where(function($brand_designer)use($brand_id){
                            $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                            $brand_designer->where('organization_id',$brand_id);
                            $brand_designer->whereHas('albums',function($album){
                                //品牌设计师筛选审核通过的方案
                                $album->where('status',Album::STATUS_PASS);
                            });
                        });
                        //旗下销售商的所有设计师
                        $query1->orWhere(function($seller_designer)use($brand_id){
                            $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                            $seller_designer->whereHas('seller',function($organization)use($brand_id){
                                $organization->where('organization_dealers.p_brand_id',$brand_id);
                            });
                            $seller_designer->whereHas('album',function($album)use($brand_id){
                                $album->where('status',Album::STATUS_PASS);
                                $album->where('status_brand',$brand_id);
                            });
                        });
                    });
                }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                    //销售商直属设计师可见性

                }else{
                    $builder->where('id',0);
                }

            }else{
                $builder->where('id',0);
            }

        }


        $datas = $builder->get();


        $datas->transform(function($v){
            $temp = new \stdClass();

            $temp->avatar = $v->detail->url_avatar;
            $temp->web_id_code = $v->web_id_code;
            $temp->technology = $v->technology;
            $temp->nickname = $v->detail->nickname;
            $temp->level = Designer::designerTitleCn($v->detail->self_designer_level);
            $temp->company_name = '';
            $organization = $v->organization;
            if($organization){
                $temp->company_name = $organization->name;
            }
            $temp->fans = 0;
            $temp->count_upload_album = 0;
            $stat = StatisticDesigner::where('designer_id',$v->id)->orderBy('id','desc')->first();
            if($stat){
                $temp->fans = intval($stat->count_faved_designer);
                $temp->count_upload_album = intval($stat->count_upload_album);
            }

            return $temp;
        });

        return $this->respDataReturn($datas);

    }
}
