<?php
/**
 * BrandScopeDesignerDataService
 * 异步获取designer方法数据筛选（品牌域专用）
 */

namespace App\Services\v1\site;

use App\Http\Services\common\StrService;
use App\Models\Album;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailDealer;
use App\Models\FavDesigner;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\StatisticDesigner;
use Illuminate\Support\Facades\DB;

class BsDesignerDataService
{

    /**
     * 调用者：品牌主页->设计师
     */
    public static function listBrandIndexDesigner($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'cityId'=>0,
            'skip'=>0,
            'take'=>6,
        ];
        
        $params = array_merge($default, $params);
        
        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $skip = $params['skip'];
        $take = $params['take'];
        $brandId = $loginBrandId;

        $city = Area::find($params['cityId']);
        $cityName = '';

        //品牌信息合法性
        if($brandId<0){
            return [];
        }
        $brand = OrganizationBrand::find($brandId);
        if(!$brand){
            return [];
        }

        if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
            //品牌设计师可见性
            //所有品牌设计师+旗下销售商的所有设计师

            $builder = DB::table('designer_details as dd')
                ->join('designers as d','dd.designer_id','=','d.id')
                //对于品牌站，通过方案状态筛选出正确的设计师列表
                ->where(function($query1)use($brandId,$request){
                    $query1->where(function($brand_designer)use($brandId){
                        //品牌设计师，则筛选有已审核通过的方案的
                        $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                        $brand_designer->where('organization_id',$brandId);
                        $brand_designer->whereExists(function ($query) {
                            //品牌设计师筛选审核通过的方案
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS);
                        });
                    });
                    $query1->orWhere(function($seller_designer)use($brandId){
                        //旗下销售商设计师，则筛选有品牌站可用的方案的
                        $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                        $seller_designer->whereExists(function ($query) use($brandId) {
                            //通过销售商关联品牌id
                            $query->select(DB::raw(1))
                                ->from('organization_dealers as od')
                                ->whereRaw('od.id = d.organization_id and od.p_brand_id = '.$brandId);
                        });
                        $seller_designer->whereExists(function ($query)use($brandId) {
                            //销售商设计师筛选品牌站可用的方案
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS.' and albums.status_brand = '.$brandId);
                        });

                    });
                })
                ->where('d.status',Designer::STATUS_ON);
        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性
            //所有品牌设计师+所属销售商的设计师+所在地可见销售商的设计师
            $builder = DB::table('designer_details as dd')
                ->join('designers as d','dd.designer_id','=','d.id')
                ->where(function($query1)use($brandId,$loginDealerId,$request){
                    //1.所有品牌设计师
                    $query1->where(function($brand_designer)use($brandId,$loginDealerId){
                        //品牌设计师，则筛选有已审核通过的方案的
                        $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                        $brand_designer->where('organization_id',$brandId);
                        $brand_designer->whereExists(function ($query) {
                            //筛选含有审核通过的方案的设计师
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS);
                        });
                    });
                    $query1->orWhere(function($seller_designer)use($brandId,$loginDealerId){
                        //2.所属销售商的设计师
                        $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                        $seller_designer->where('organization_id',$loginDealerId);
                        $seller_designer->whereExists(function ($query) {
                            //筛选含有审核通过的方案的设计师
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS);
                        });

                    });

                    //3.所在地可见销售商的设计师

                    //所在地
                    $locationInfo = LocationService::getClientCity($request);
                    $cityId = 0;
                    if(isset($locationInfo) && $locationInfo['city_id']){
                        $cityId = $locationInfo['city_id'];
                    }
                    if($cityId>0){
                        $query1->orWhere(function($seller_designer)use($brandId,$loginDealerId,$cityId,$request){

                            //符合条件的销售商id合集（默认放进所属销售商）
                            $legalDealerIds =  [];

                            //获取所在地可见的销售商ids
                            if($cityId>0){
                                $legalDealerIds = DetailDealer::query()
                                    ->whereHas('dealer',function($dealer) use($brandId){
                                        $dealer->where('p_brand_id',$brandId);
                                    })//所在地可见的销售商需要在本品牌内
                                    ->whereRaw('(area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" )')
                                    ->get(['dealer_id'])->pluck('dealer_id')->toArray();
                            }

                            $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                            $seller_designer->whereIn('organization_id',$legalDealerIds);
                            $seller_designer->whereExists(function ($query) {
                                //筛选含有审核通过的方案的设计师
                                $query->select(DB::raw(1))
                                    ->from('albums')
                                    ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS);
                            });

                        });
                    }


                })
                ->where('d.status',Designer::STATUS_ON);

        }else{
            //其他
            return [];
        }

        if($city) {
            $builder->where(['dd.area_serving_city'=>$params['cityId']]);
            $cityName = $city->shortname;
        }

        $builder->groupBy('d.id');

        $builder->orderBy('dd.top_status','desc');
        $builder->orderBy('dd.point_focus','desc');
        $builder->orderBy('dd.point_experience','desc');
        $builder->orderBy('dd.designer_id','desc');

        $data_count = $builder->count();

        $builder->skip($skip)
            ->take($take);

        $builder->select([
            'designer_id','web_id_code','nickname','url_avatar','self_working_year',
            'area_serving_city','approve_realname','top_status','self_organization'
        ]);

        $res = $builder->get();

        $res->transform(function($v)use($loginDesigner,$cityName){
            if($cityName=='') {
                $city = Area::find($v->area_serving_city);
                if ($city)
                    $v->city = $city->shortname;
            }
            else{
                $v->city = $cityName;
            }
            $v->self_working_year = StrService::str_num_to_char($v->self_working_year).'年';
            $v->url_avatar = url($v->url_avatar);
            $v->identity = $v->approve_realname==DesignerDetail::APPROVE_REALNAME_YES?true:false;
            $v->hot = $v->top_status==DesignerDetail::TOP_STATUS_YES?true:false;

            unset($v->id);
            return $v;
        });

        return $res;
    }


    /**
     * 调用者：设计师列表主页->列表数据
     */
    public static function listDesignerIndexData($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'take'=>12,
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $take = $params['take'];
        $brandId = $loginBrandId;

        //品牌信息合法性
        if($brandId<0){
            return [];
        }
        $brand = OrganizationBrand::find($brandId);
        if(!$brand){
            return [];
        }

        if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
            //品牌设计师可见性
            //所有品牌设计师+旗下销售商的所有设计师

            $builder = DB::table('designer_details as dd')
                ->join('designers as d','dd.designer_id','=','d.id')
                //对于品牌站，通过方案状态筛选出正确的设计师列表
                ->where(function($query1)use($brandId){
                    $query1->where(function($brand_designer)use($brandId){
                        //品牌设计师，则筛选有已审核通过的方案的
                        $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                        $brand_designer->where('organization_id',$brandId);
                        $brand_designer->whereExists(function ($query) {
                            //品牌设计师筛选审核通过的方案
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS);
                        });
                    });
                    $query1->orWhere(function($seller_designer)use($brandId){
                        //旗下销售商设计师，则筛选有品牌站可用的方案的
                        $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                        $seller_designer->whereExists(function ($query) use($brandId) {
                            //通过销售商关联品牌id
                            $query->select(DB::raw(1))
                                ->from('organization_dealers as od')
                                ->whereRaw('od.id = d.organization_id and od.p_brand_id = '.$brandId);
                        });
                        $seller_designer->whereExists(function ($query)use($brandId) {
                            //销售商设计师筛选品牌站可用的方案
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS.' and albums.status_brand = '.$brandId);
                        });

                    });
                });


        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性
            //所有品牌设计师+所属销售商的设计师+所在地可见销售商的设计师
            $builder = DB::table('designer_details as dd')
                ->join('designers as d','dd.designer_id','=','d.id')
                ->where(function($query1)use($brandId,$loginDealerId,$request){
                    //1.所有品牌设计师
                    $query1->where(function($brand_designer)use($brandId,$loginDealerId){
                        //品牌设计师，则筛选有已审核通过的方案的
                        $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                        $brand_designer->where('organization_id',$brandId);
                        $brand_designer->whereExists(function ($query) {
                            //筛选含有审核通过的方案的设计师
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS.' and albums.visible_status = '.Album::VISIBLE_STATUS_ON);
                        });
                    });

                    //2.所属销售商的设计师
                    $query1->orWhere(function($seller_designer)use($brandId,$loginDealerId){
                        $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                        $seller_designer->where('organization_id',$loginDealerId);
                        $seller_designer->whereExists(function ($query) {
                            //筛选含有审核通过的方案的设计师
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS.' and albums.visible_status = '.Album::VISIBLE_STATUS_ON);
                        });

                    });

                    //3.所在地可见销售商的设计师
                    //所在地
                    $locationInfo = LocationService::getClientCity($request);
                    $cityId = 0;
                    if(isset($locationInfo) && $locationInfo['city_id']){
                        $cityId = $locationInfo['city_id'];
                    }
                    if($cityId>0){
                        $query1->orWhere(function($seller_designer)use($brandId,$loginDealerId,$cityId,$request){

                            //符合条件的销售商id合集（默认放进所属销售商）
                            $legalDealerIds =  [];

                            //获取所在地可见的销售商ids
                            if($cityId>0){
                                $legalDealerIds = DetailDealer::query()
                                    ->whereHas('dealer',function($dealer) use($brandId){
                                        $dealer->where('p_brand_id',$brandId);
                                    })//所在地可见的销售商需要在本品牌内
                                    ->whereRaw('(area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" )')
                                    ->get(['dealer_id'])->pluck('dealer_id')->toArray();
                            }

                            $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                            $seller_designer->whereIn('organization_id',$legalDealerIds);
                            $seller_designer->whereExists(function ($query) {
                                //筛选含有审核通过的方案的设计师
                                $query->select(DB::raw(1))
                                    ->from('albums')
                                    ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS.' and albums.visible_status = '.Album::VISIBLE_STATUS_ON);
                            });

                        });
                    }


                });

        }else{
            //其他
            return [];
        }

        //搜索名称
        if($search = $request->input('search','')){
            $like = '%'.$search.'%';

            $builder->where(function($query) use ($like){
                $query->where('dd.nickname','like',$like);
            });
        }

        //是否擅长风格
        $style_id = $request->input('stl',null);
        if($style_id){
            $builder->whereExists(function ($query) use($style_id) {
                $query->select(DB::raw(1))
                    ->from('styles')
                    ->join('designer_styles','styles.id','=','designer_styles.style_id')
                    ->whereRaw('d.id = designer_styles.designer_id')
                    ->whereRaw('styles.id = '.$style_id);
            });
        }

        //是否擅长空间
        $space_id = $request->input('sp',null);
        if($space_id){
            $builder->whereExists(function ($query) use($space_id) {
                $query->select(DB::raw(1))
                    ->from('spaces')
                    ->join('designer_spaces','spaces.id','=','designer_spaces.space_id')
                    ->whereRaw('d.id = designer_spaces.designer_id')
                    ->whereRaw('spaces.id = '.$space_id);
            });
        }

        //是否筛选等级
        $level = $request->input('lv',null);
        if($level){
            $builder->where('dd.self_designer_level','=',$level);
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

        //筛选状态
        $builder->where('d.status',Designer::STATUS_ON);

        $builder->select([
            'd.id','d.web_id_code',
            DB::raw('( select count_upload_album from statistic_designers as sd
                        where sd.designer_id = d.id order by sd.id desc limit 1) as  count_upload_album'),
            'dd.designer_id','dd.self_designer_level','dd.url_avatar','dd.nickname',
            'dd.area_serving_province','dd.area_serving_city','dd.point_focus',
            'dd.count_visit','dd.count_fav'
        ]);

        $res = $builder->paginate($take);

        $res->transform(function($v)use($loginDesigner){
            $v->focused = false;
            if($loginDesigner){
                $focused = FavDesigner::where('target_designer_id',$v->id)->where('designer_id',$loginDesigner->id)->first();
                if($focused){ $v->focused = true; }
            }

            //擅长风格
            $v->styles_text = '';
            $styles = DB::table('styles as s')
                ->join('designer_styles as ds','ds.style_id','=','s.id')
                ->where('ds.designer_id',$v->id)
                ->get();
            $styles = $styles->pluck('name')->toArray();
            if(is_array($styles) && count($styles)>0){
                $v->styles_text = implode(',',$styles);
            }
            //粉丝数、设计方案数
            $v->fans = 0;
            $v->count_upload_album = 0;
            $stat = StatisticDesigner::query()->where('designer_id',$v->id)->orderBy('id','desc')->first();
            if($stat){
                $v->fans = intval($stat->count_faved_designer);
                $v->count_upload_album = intval($stat->count_upload_album);
            }
            //3个最新设计方案封面、标题
            $v->albums = [];
            $albums = Album::query()
                ->where('designer_id',$v->id)
                ->where('status',Album::STATUS_PASS)
                ->where('visible_status',Album::VISIBLE_STATUS_ON)
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

        return $res;
    }


    /**
     * 调用者：设计师列表主页->优秀设计师数据
     */
    public static function listDesignerIndexNiceData($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'take'=>5,
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $take = $params['take'];
        $brandId = $loginBrandId;

        //品牌信息合法性
        if($brandId<0){
            return [];
        }
        $brand = OrganizationBrand::find($brandId);
        if(!$brand){
            return [];
        }

        if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
            //品牌设计师可见性
            //所有品牌设计师+旗下销售商的所有设计师

            $builder = DB::table('designer_details as dd')
                ->join('designers as d','dd.designer_id','=','d.id')
                //对于品牌站，通过方案状态筛选出正确的设计师列表
                ->where(function($query1)use($brandId){
                    $query1->where(function($brand_designer)use($brandId){
                        //品牌设计师，则筛选有已审核通过的方案的
                        $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                        $brand_designer->where('organization_id',$brandId);
                        $brand_designer->whereExists(function ($query) {
                            //品牌设计师筛选审核通过的方案
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS);
                        });
                    });
                    $query1->orWhere(function($seller_designer)use($brandId){
                        //旗下销售商设计师，则筛选有品牌站可用的方案的
                        $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                        $seller_designer->whereExists(function ($query) use($brandId) {
                            //通过销售商关联品牌id
                            $query->select(DB::raw(1))
                                ->from('organization_dealers as od')
                                ->whereRaw('od.id = d.organization_id and od.p_brand_id = '.$brandId);
                        });
                        $seller_designer->whereExists(function ($query)use($brandId) {
                            //销售商设计师筛选品牌站可用的方案
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS.' and albums.status_brand = '.$brandId);
                        });

                    });
                });


        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性
            //所有品牌设计师+所属销售商的设计师+所在地可见销售商的设计师
            $builder = DB::table('designer_details as dd')
                ->join('designers as d','dd.designer_id','=','d.id')
                ->where(function($query1)use($brandId,$loginDealerId,$request){
                    //1.所有品牌设计师
                    $query1->where(function($brand_designer)use($brandId,$loginDealerId){
                        //品牌设计师，则筛选有已审核通过的方案的
                        $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                        $brand_designer->where('organization_id',$brandId);
                        $brand_designer->whereExists(function ($query) {
                            //筛选含有审核通过的方案的设计师
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS);
                        });
                    });

                    //2.所属销售商的设计师
                    $query1->orWhere(function($seller_designer)use($brandId,$loginDealerId){
                        $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                        $seller_designer->where('organization_id',$loginDealerId);
                        $seller_designer->whereExists(function ($query) {
                            //筛选含有审核通过的方案的设计师
                            $query->select(DB::raw(1))
                                ->from('albums')
                                ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS);
                        });

                    });

                    //3.所在地可见销售商的设计师
                    //所在地
                    $locationInfo = LocationService::getClientCity($request);
                    $cityId = 0;
                    if(isset($locationInfo) && $locationInfo['city_id']){
                        $cityId = $locationInfo['city_id'];
                    }
                    if($cityId>0){
                        $query1->orWhere(function($seller_designer)use($brandId,$loginDealerId,$cityId,$request){

                            //符合条件的销售商id合集（默认放进所属销售商）
                            $legalDealerIds =  [];

                            //获取所在地可见的销售商ids
                            if($cityId>0){
                                $legalDealerIds = DetailDealer::query()
                                    ->whereHas('dealer',function($dealer) use($brandId){
                                        $dealer->where('p_brand_id',$brandId);
                                    })//所在地可见的销售商需要在本品牌内
                                    ->whereRaw('(area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" )')
                                    ->get(['dealer_id'])->pluck('dealer_id')->toArray();
                            }

                            $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                            $seller_designer->whereIn('organization_id',$legalDealerIds);
                            $seller_designer->whereExists(function ($query) {
                                //筛选含有审核通过的方案的设计师
                                $query->select(DB::raw(1))
                                    ->from('albums')
                                    ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS);
                            });

                        });
                    }


                });
        }else{
            //其他
            return [];
        }

        //筛选状态
        $builder->where('d.status',Designer::STATUS_ON);
        $builder->where('top_brand_status',Designer::TOP_BRAND_STATUS_ON);

        //排序
        $builder->inRandomOrder();

        $builder->select([
            'd.id','d.web_id_code','d.organization_type','d.organization_id',
            'dd.url_avatar','dd.nickname','dd.self_designer_level',
        ]);

        $res = $builder->limit($take)->get();

        $res->transform(function($v)use($loginDesigner){
            $temp = new \stdClass();

            $temp->avatar = $v->url_avatar;
            $temp->web_id_code = $v->web_id_code;
            $temp->nickname = $v->nickname;
            $temp->level = Designer::designerTitleCn($v->self_designer_level);
            $temp->company_name = '';
            $organization = null;
            if($v->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                $organization = OrganizationBrand::find($v->organization_id);
            }else if($v->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                $organization = OrganizationDealer::find($v->organization_id);
            }
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

        return $res;
    }

}