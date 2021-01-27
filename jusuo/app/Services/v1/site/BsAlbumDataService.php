<?php
/**
 * BrandScopeAlbumDataService
 * 异步获取album方法数据筛选（品牌域专用）
 */

namespace App\Services\v1\site;


use App\Models\Album;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\FavAlbum;
use App\Models\HouseType;
use App\Models\LikeAlbum;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\SpaceType;
use App\Models\Style;
use App\Services\v1\site\AlbumService;
use Illuminate\Support\Facades\DB;

class BsAlbumDataService
{

    /**
     * 调用者：品牌主页->设计方案
     */
    public static function listBrandIndexAlbum($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginBrandId'=>'',
            'loginDealerId'=>0,
            'styleId'=>0,
            'skip'=>0,
            'take'=>6
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $styleId = $params['styleId'];
        $skip = $params['skip'];
        $take = $params['take'];

        //品牌信息合法性
        if($loginBrandId<0){
            return [];
        }

        $brand = OrganizationBrand::find($loginBrandId);
        if(!$brand){
            return [];
        }

        //******  筛选出来的方案与设计师上架是否无关

        if($params['loginDesigner']->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
            //品牌设计师可见性
            //品牌设计师的所有展示方案+旗下所有销售商且被品牌显示的方案
            $builder = DB::table('albums as a')
                ->select(['a.*','sa.album_id'])
                ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where(function($where1)use($loginBrandId){
                    $where1->where(function($query) use($loginBrandId){

                        $query->whereExists(function($query1) use($loginBrandId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                                ->where('d.organization_id',$loginBrandId);
                        });
                        //展示上架方案
                        $query->where('a.status_brand',$loginBrandId);

                    })
                        ->orWhere(function($query) use($loginBrandId){
                            $query->whereExists(function($query1) use($loginBrandId){
                                $query1->select(DB::raw(1))
                                    ->from('designers as d')
                                    ->join('organization_dealers as od','d.organization_id','=','od.id')
                                    ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                    ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)
                                    ->where('od.p_brand_id',$loginBrandId)
                                    ->where('a.status_brand',$loginBrandId);
                            });
                        });
                });


        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性
            //品牌设计师的所有方案+所在地可见销售商且被品牌显示的方案+所属销售商且被品牌显示的方案

            //所在地
            $locationInfo = LocationService::getClientCity($request);
            $cityId = 0;
            if(isset($locationInfo) && $locationInfo['city_id']){
                $cityId = $locationInfo['city_id'];
            }
            $builder = DB::table('albums as a')
                ->select(['a.*','sa.album_id'])
                ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where(function($where1)use($loginBrandId,$cityId,$loginDealerId){
                    $where1->where(function($query) use($loginBrandId){
                        //品牌设计师的所有方案
                        $query->whereExists(function($query1) use($loginBrandId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                                ->where('d.organization_id',$loginBrandId);

                        });
                        //展示上架方案
                        $query->where('a.status_brand',$loginBrandId);
                    });

                    if($cityId>0){
                        $where1->orWhere(function($query)use($loginBrandId,$cityId,$loginDealerId) {
                            //所在地可见销售商且被品牌显示的方案
                            $query->whereExists(function($query1) use($loginBrandId,$cityId,$loginDealerId){
                                $query1->select(DB::raw(1))
                                    ->from('designers as d')
                                    ->join('organization_dealers as od','od.id','=','d.organization_id')
                                    ->join('detail_dealers as dd','od.id','=','dd.dealer_id')
                                    ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                    ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                                    ->whereRaw('(dd.area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%")')  //所在地
                                    ->where('a.status_brand',$loginBrandId)
                                    ->where('dd.dealer_id','<>',$loginDealerId) //下面会查所属销售商，所以这里筛选掉
                                    ->where('od.p_brand_id',$loginBrandId); //所在地可见销售商也必须是本品牌的
                            });
                        });
                    }

                    $where1->orWhere(function($query)use($loginBrandId,$cityId,$loginDealerId) {
                        //所属销售商且被品牌显示的方案
                        $query->whereExists(function($query1) use($loginBrandId,$cityId,$loginDealerId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                                ->where('d.organization_id',$loginDealerId)  //所属销售商
                                ->where('a.status_brand',$loginBrandId);
                        });
                    });
                });


        }else{
            return [];
        }

        //筛选风格
        if($styleId>0){
            $style = Style::find($styleId);
            if($style) {
                $builder->where('sa.style', 'like', '%' . AlbumService::JOINER . $styleId . AlbumService::JOINER . '%');
            }
        }

        $builder->where(['status'=>Album::STATUS_PASS]);

        $builder->orderBy('sa.weight_sort','desc');
        $builder->orderBy('a.id','desc');

        $builder->groupBy("a.id");

        $builder_count = $builder->count();

        $builder->skip($skip)
            ->take($take);

        $builder->select([
            'a.id','a.title','a.count_area','a.designer_id','a.count_visit','a.count_praise',
            'a.count_fav','a.photo_cover','a.description_design','a.type','a.web_id_code'
        ]);

        $res = $builder->get();

        $res->transform(function($v)use($loginDesigner){
            //设计师信息
            $designer = Designer::find($v->designer_id);
            if($designer){
                $designerDetail = DesignerDetail::where('designer_id',$v->designer_id)->first();
                if($designerDetail){
                    $v->designerPhoto = url($designerDetail->url_avatar);
                    $v->designer = $designerDetail->nickname;
                }
            }
            //是否方案源
            $v->panorama = $v->type==Album::TYPE_KUJIALE_SOURCE?true:false;
            //补全封面图路径
            $v->photo_cover = url($v->photo_cover);
            //登录设计师是否点赞或收藏该方案
            $likeAlbum = false;
            $favAlbum = false;
            if (LikeAlbum::where(['designer_id' => $loginDesigner->id, 'album_id' => $v->id])->count()) {
                $likeAlbum = true;
            }
            if (FavAlbum::where(['designer_id' => $loginDesigner->id, 'album_id' => $v->id])->count()) {
                $favAlbum = true;
            }
            $v->liked = $likeAlbum;
            $v->collected = $favAlbum;

            //unset($v->id);
            return $v;
        });

        return [
            'data'=>$res,
            'total'=>$builder_count,
            'params'=>$params
        ];
    }

    /**
     * 调用者：方案列表主页->列表数据异步获取
     */
    public static function listAlbumIndexData($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'take'=>30,
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $take = $params['take'];

        //品牌信息合法性
        if($loginBrandId<0){
            return [];
        }

        $brand = OrganizationBrand::find($loginBrandId);
        if(!$brand){
            return [];
        }

        //******  筛选出来的方案与设计师上架是否无关

        if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
            //品牌设计师可见性
            //品牌设计师的所有展示方案+旗下所有销售商的方案
            $builder = DB::table('albums as a')
                ->select(['a.*','sa.album_id'])
                ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where(function($where)use($loginBrandId){
                    $where->where(function($query) use($loginBrandId){
                        $query->whereExists(function($query1) use($loginBrandId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                                ->where('d.organization_id',$loginBrandId)
                                ->where('a.status',Album::STATUS_PASS); //必须是已审核通过的

                        });
                        //展示上架方案
                        $query->where('a.status_brand',$loginBrandId);
                    });
                    $where->orWhere(function($query) use($loginBrandId){
                        $query->whereExists(function($query1) use($loginBrandId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->join('organization_dealers as od','od.id','=','d.organization_id')
                                ->join('detail_dealers as dd','od.id','=','dd.dealer_id')                            ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)
                                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                ->where('a.status_brand',$loginBrandId)
                                ->where('od.p_brand_id',$loginBrandId);

                        });
                    });
                });


        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性
            //品牌设计师的所有展示方案+所在地可见销售商的方案+所属销售商的方案


            //所在地
            $locationInfo = LocationService::getClientCity($request);
            $cityId = 0;
            if(isset($locationInfo) && $locationInfo['city_id']){
                $cityId = $locationInfo['city_id'];
            }
            $builder = DB::table('albums as a')
                ->select(['a.*','sa.album_id'])
                ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where(function($where)use($loginBrandId,$cityId,$loginDealerId){
                    $where->where(function($query) use($loginBrandId){
                        //品牌设计师的所有展示方案
                        $query->whereExists(function($query1) use($loginBrandId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                                ->where('d.organization_id',$loginBrandId)
                                ->where('a.status',Album::STATUS_PASS); //必须是已审核通过的;
                        });
                        $query->where('a.status_brand',$loginBrandId);
                    });

                    if($cityId>0){
                        $where->orWhere(function($query)use($loginBrandId,$cityId,$loginDealerId) {
                            //所在地可见销售商的方案
                            $query->whereExists(function($query1) use($loginBrandId,$cityId,$loginDealerId){
                                $query1->select(DB::raw(1))
                                    ->from('designers as d')
                                    ->join('organization_dealers as od','od.id','=','d.organization_id')
                                    ->join('detail_dealers as dd','od.id','=','dd.dealer_id')
                                    ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                    ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                                    ->whereRaw('(dd.area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%")')  //所在地
                                    ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                    ->where('dd.dealer_id','<>',$loginDealerId) //下面会查所属销售商，所以这里筛选掉
                                    ->where('od.p_brand_id',$loginBrandId); //所在地可见销售商也要在品牌内
                            });
                        });
                    }

                    $where->orWhere(function($query)use($loginBrandId,$cityId,$loginDealerId) {
                        //所属销售商的方案
                        $query->whereExists(function($query1) use($loginBrandId,$cityId,$loginDealerId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                                ->where('d.organization_id',$loginDealerId)  //所属销售商
                                ->where('a.status',Album::STATUS_PASS); //必须是已审核通过的
                        });
                    });

                });




        }else{
            return [];
        }

        //筛选设计师
        if($requestDesignerId = $request->input('dsn',null)){
            $designer = Designer::where('web_id_code',$requestDesignerId)->first();
            if($designer) {
                $builder->where('a.designer_id', $designer->id);
            }else{
                return [];
            }
        }

        //筛选销售商
        if($requestDealerId = $request->input('dlr',null)){
            $dealer = OrganizationDealer::where('web_id_code',$requestDealerId)->first();
            if($dealer) {
                $builder->whereExists(function($query1) use($dealer){
                    $query1->select(DB::raw(1))
                        ->from('designers as d')
                        ->join('organization_dealers as od','od.id','=','d.organization_id')
                        ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                        ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                        ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                        ->where('od.id',$dealer->id);
                });
            }else{
                return [];
            }
        }

        //筛选风格
        if($styleId = $request->input('stl',null)){
            $style = Style::find($styleId);
            if($style) {
                $builder->where('sa.style', 'like', '%' . AlbumService::JOINER . $styleId . AlbumService::JOINER . '%');
            }
        }

        //是否提交户型
        if($houseTypeId = $request->input('ht',null)){
            $houseType = HouseType::find($houseTypeId);
            if($houseType){
                $builder->where('sa.house_type','like','%|'.$houseTypeId.'|%');
            }
        }

        //是否提交空间类型
        if($spaceTypeId = $request->input('spt',null)){
            $houseType = SpaceType::find($spaceTypeId);
            if($houseType){
                $builder->where('sa.space_type','like','%|'.$spaceTypeId.'|%');
            }
        }

        //是否提交面积
        if($countArea = $request->input('ca',null)){
            $area_info = explode('_',$countArea);
            if(isset($area_info[0]) && isset($area_info[1])){
                if($area_info[1]=='m'){
                    $builder->where('count_area','>',$area_info[0]);
                }else{
                    $min = floatval($area_info[0]);
                    $max = floatval($area_info[1]);
                    $builder->where('count_area','>',$min);
                    $builder->where('count_area','<=',$max);
                }
            }
        }

        //是否精选方案
        if($isRepresentativeWork = $request->input('isrw',0)){
            //是否精选方案改为显示品牌置顶的方案20200426
            $builder->where('a.top_status_brand',Album::TOP_BRAND_ON);
        }

        //是否提交关键字
        if($keyword = $request->input('k',null)){
            $builder->whereRaw('(sa.title like "%'.$keyword.'%" or sa.product_ceramic like "%'.$keyword.'%")');
        }

        //排序
        if($order = $request->input('order','')){
            if(preg_match('/^(.+)_(asc|desc)$/',$order,$m)){
                if(in_array($m[1],['comples','pop','time'])){
                    if($m[1] == 'comples'){
                        $builder->orderBy('sa.weight_sort',$m[2]);
                    }else if($m[1] == 'pop'){
                        $builder->orderBy('a.count_visit',$m[2])->orderBy('a.count_praise',$m[2])->orderBy('a.count_fav',$m[2]);
                    }else if($m[1] == 'time'){
                        $builder->orderBy('a.created_at',$m[2]);
                    }else{
                        $builder->orderBy('sa.weight_sort',$m[2]);
                    }
                }
            }
        }

        $builder->where('period_status',Album::PERIOD_STATUS_FINISH);

        $builder->orderBy('sa.weight_sort','desc');
        $builder->orderBy('a.id','desc');


        $builder->select([
            'a.id','a.web_id_code','a.photo_cover','a.count_area','a.title','a.count_visit','a.count_praise',
            'a.count_fav','a.designer_id','a.type'

        ]);

        $res = $builder->paginate($take);

        $res->transform(function($v)use($loginDesigner){
            //设计师信息
            $v->designerIdentity = false;
            $v->designerHot = false;
            $v->designerAvatar = '';
            $v->designerNickname = '';
            $designer = Designer::find($v->designer_id);
            if($designer){
                $designerDetail = DesignerDetail::where('designer_id',$v->designer_id)->first();
                if($designerDetail){
                    $v->designerAvatar = url($designerDetail->url_avatar);
                    $v->designerNickname = $designerDetail->nickname;
                    if($designerDetail->approve_realname == DesignerDetail::APPROVE_REALNAME_YES){
                        $v->designerIdentity = true;
                    }
                    if($designerDetail->top_status == DesignerDetail::TOP_STATUS_YES){
                        $v->designerHot = true;
                    }
                }
            }
            //是否方案源
            $v->panorama = $v->type==Album::TYPE_KUJIALE_SOURCE?true:false;
            //补全封面图路径
            $v->photo_cover = url($v->photo_cover);
            //登录设计师是否点赞或收藏该方案
            $likeAlbum = false;
            $favAlbum = false;
            if (LikeAlbum::where(['designer_id' => $loginDesigner->id, 'album_id' => $v->id])->count()) {
                $likeAlbum = true;
            }
            if (FavAlbum::where(['designer_id' => $loginDesigner->id, 'album_id' => $v->id])->count()) {
                $favAlbum = true;
            }
            $v->liked = $likeAlbum;
            $v->collected = $favAlbum;

            //unset($v->designer_id);
            unset($v->id);
            return $v;
        });

        return $res;
    }

    /**
     * 调用者：方案详情->相似方案
     */
    public static function listAlbumDetailSimiliar($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'styleIds'=>[],
            'skip'=>0,
            'take'=>6
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $styleIds = $params['styleIds'];
        $skip = $params['skip'];
        $take = $params['take'];

        $preview_brand_id = session('preview_brand_id');
        if(isset($preview_brand_id) && $preview_brand_id){
            //预览获取数据
            //品牌设计师的所有方案+旗下所有销售商的方案
            $builder = DB::table('albums as a')
                ->select(['a.*','sa.album_id'])
                ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where(function($query) use($loginBrandId,$preview_brand_id){
                    $query->whereExists(function($query1) use($loginBrandId,$preview_brand_id){
                        $query1->select(DB::raw(1))
                            ->from('designers as d')
                            ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                            ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                            ->where('d.organization_id',$preview_brand_id)
                            ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                            ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                    });
                })
                ->orWhere(function($query) use($loginBrandId,$preview_brand_id){
                    $query->whereExists(function($query1) use($loginBrandId,$preview_brand_id){
                        $query1->select(DB::raw(1))
                            ->from('designers as d')
                            ->join('organization_dealers as od','d.organization_id','=','od.id')
                            ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                            ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)
                            ->where('od.p_brand_id',$preview_brand_id)
                            ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                            ->where('a.visible_status',Album::VISIBLE_STATUS_ON);

                    });
                });


        }else{
            //正常获取数据
            //品牌信息合法性
            if($loginBrandId<0){
                return [];
            }

            $brand = OrganizationBrand::find($loginBrandId);
            if(!$brand){
                return [];
            }


            if($params['loginDesigner']->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
                //品牌设计师可见性
                //品牌设计师的所有方案+旗下所有销售商的方案
                $builder = DB::table('albums as a')
                    ->select(['a.*','sa.album_id'])
                    ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                    ->where(function($query) use($loginBrandId){
                        $query->whereExists(function($query1) use($loginBrandId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                                ->where('d.organization_id',$loginBrandId)
                                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                        });
                    })
                    ->orWhere(function($query) use($loginBrandId){
                        $query->whereExists(function($query1) use($loginBrandId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->join('organization_dealers as od','d.organization_id','=','od.id')
                                ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)
                                ->where('od.p_brand_id',$loginBrandId)
                                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                ->where('a.visible_status',Album::VISIBLE_STATUS_ON);

                        });
                    });

            }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
                //销售商直属设计师可见性
                //品牌所有设计师的方案+所在地可见的销售商的设计师的方案+所属销售商的设计师的方案
                $builder = DB::table('albums as a')
                    ->select(['a.*','sa.album_id'])
                    ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                    //1.品牌所有设计师的方案
                    ->where(function($query) use($loginBrandId){
                        $query->whereExists(function($query1) use($loginBrandId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                                ->where('d.organization_id',$loginBrandId)
                                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                        });
                    });

                //2.所在地可见的销售商的设计师的方案
                //所在地
                $locationInfo = LocationService::getClientCity($request);
                $cityId = 0;
                if(isset($locationInfo) && $locationInfo['city_id']){
                    $cityId = $locationInfo['city_id'];
                }
                if($cityId>0){
                    $builder->orWhere(function($query)use($loginBrandId,$cityId,$loginDealerId) {
                        //所在地可见销售商且被品牌显示的方案
                        $query->whereExists(function($query1) use($loginBrandId,$cityId,$loginDealerId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->join('organization_dealers as od','od.id','=','d.organization_id')
                                ->join('detail_dealers as dd','od.id','=','dd.dealer_id')
                                ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                                ->whereRaw('(dd.area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%")')  //所在地
                                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                ->where('a.visible_status',Album::VISIBLE_STATUS_ON)
                                ->where('dd.dealer_id','<>',$loginDealerId) //下面会查所属销售商，所以这里筛选掉
                                ->where('od.p_brand_id',$loginBrandId); //所在地可见的销售商也要在品牌内
                        });
                    });
                }

                //3.所属销售商的设计师的方案
                $builder->orWhere(function($query)use($loginDealerId) {
                    $query->whereExists(function($query1) use($loginDealerId){
                        $query1->select(DB::raw(1))
                            ->from('designers as d')
                            ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                            ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                            ->where('d.organization_id',$loginDealerId)  //所属销售商
                            ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                            ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                    });
                });


            }else{
                return [];
            }
        }



        //筛选风格
        $builder->where(function($query)use($styleIds){
            foreach($styleIds as $key=>$styleId){
                if($key==0){
                    $query->where('sa.style', 'like', '%' . AlbumService::JOINER . $styleId . AlbumService::JOINER . '%');
                }else{
                    $query->orWhere('sa.style', 'like', '%' . AlbumService::JOINER . $styleId . AlbumService::JOINER . '%');
                }
            }
        });

        $builder->orderBy('sa.weight_sort','desc');
        $builder->orderBy('a.id','desc');

        $builder_count = $builder->count();

        $builder->skip($skip)
            ->take($take);

        $builder->select([
            'a.count_area','a.web_id_code','a.photo_cover','a.title','a.count_visit','a.designer_id'
        ]);

        $res = $builder->get();

        $res->transform(function($v)use($loginDesigner){
            //设计师信息
            $designer = Designer::find($v->designer_id);
            if($designer){
                $designerDetail = DesignerDetail::where('designer_id',$v->designer_id)->first();
                if($designerDetail){
                    $v->designerAvatar = url($designerDetail->url_avatar);
                    $v->designerNickname = $designerDetail->nickname;
                }
            }

            unset($v->designer_id);
            return $v;
        });

        return $res;
    }

    /**
     * 调用者：产品详情->相关方案
     */
    public static function listProductDetailAlbum($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'targetProductId'=>0,
            'skip'=>0,
            'take'=>6
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $targetProductId = $params['targetProductId'];
        $loginDealerId = $params['loginDealerId'];
        $skip = $params['skip'];
        $take = $params['take'];

        $preview_brand_id = session('preview_brand_id');
        if(isset($preview_brand_id) && $preview_brand_id) {
            //预览获取数据
            //品牌设计师的所有方案+旗下所有销售商的方案

            $builder = DB::table('albums as a')
                ->select(['a.*','sa.album_id'])
                ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where(function($where)use($preview_brand_id){
                    $where->where(function($query) use($preview_brand_id){
                        $query->whereExists(function($query1) use($preview_brand_id){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                                ->where('d.organization_id',$preview_brand_id)
                                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                        });
                    })
                        ->orWhere(function($query) use($preview_brand_id){
                            $query->whereExists(function($query1) use($preview_brand_id){
                                $query1->select(DB::raw(1))
                                    ->from('designers as d')
                                    ->join('organization_dealers as od','d.organization_id','=','od.id')
                                    ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                    ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)
                                    ->where('od.p_brand_id',$preview_brand_id)
                                    ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                    ->where('a.visible_status',Album::VISIBLE_STATUS_ON);

                            });
                        });
                });

        }else{
            //正常获取数据

            //品牌信息合法性
            if($loginBrandId<0){
                return [];
            }

            $brand = OrganizationBrand::find($loginBrandId);
            if(!$brand){
                return [];
            }


            if($params['loginDesigner']->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
                //品牌设计师可见性
                //品牌设计师的所有方案+旗下所有销售商的方案

                $builder = DB::table('albums as a')
                    ->select(['a.*','sa.album_id'])
                    ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                    ->where(function($where)use($loginBrandId){
                        $where->where(function($query) use($loginBrandId){
                            $query->whereExists(function($query1) use($loginBrandId){
                                $query1->select(DB::raw(1))
                                    ->from('designers as d')
                                    ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                                    ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                                    ->where('d.organization_id',$loginBrandId)
                                    ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                    ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                            });
                        })
                        ->orWhere(function($query) use($loginBrandId){
                            $query->whereExists(function($query1) use($loginBrandId){
                                $query1->select(DB::raw(1))
                                    ->from('designers as d')
                                    ->join('organization_dealers as od','d.organization_id','=','od.id')
                                    ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                    ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)
                                    ->where('od.p_brand_id',$loginBrandId)
                                    ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                    ->where('a.visible_status',Album::VISIBLE_STATUS_ON);

                            });
                        });
                    });


            }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
                //销售商直属设计师可见性
                //品牌设计师的所有方案+所在地可见销售商的方案+所属销售商的方案

                //所在地
                $locationInfo = LocationService::getClientCity($request);
                $cityId = 0;
                if(isset($locationInfo) && $locationInfo['city_id']){
                    $cityId = $locationInfo['city_id'];
                }
                $builder = DB::table('albums as a')
                    ->select(['a.*','sa.album_id'])
                    ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                    ->where(function($where) use($loginBrandId,$cityId,$loginDealerId){
                        $where->where(function($query) use($loginBrandId,$cityId){
                            //品牌设计师的所有方案
                            $query->whereExists(function($query1) use($loginBrandId){
                                $query1->select(DB::raw(1))
                                    ->from('designers as d')
                                    ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                                    ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                                    ->where('d.organization_id',$loginBrandId)
                                    ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                    ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                            });
                        });

                        if($cityId>0){
                            $where->orWhere(function($query)use($loginBrandId,$cityId,$loginDealerId) {
                                //所在地可见销售商的方案
                                $query->whereExists(function($query1) use($loginBrandId,$cityId,$loginDealerId){
                                    $query1->select(DB::raw(1))
                                        ->from('designers as d')
                                        ->join('organization_dealers as od','od.id','=','d.organization_id')
                                        ->join('detail_dealers as dd','od.id','=','dd.dealer_id')
                                        ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                        ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                                        ->whereRaw('(dd.area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%")')  //所在地
                                        ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                        ->where('a.visible_status',Album::VISIBLE_STATUS_ON)
                                        ->where('dd.dealer_id','<>',$loginDealerId) //下面会查所属销售商，所以这里筛选掉
                                        ->where('od.p_brand_id',$loginBrandId);  //所在地可见销售商也要在本品牌内
                                });
                            });
                        }

                        $where->orWhere(function($query)use($loginBrandId,$cityId,$loginDealerId) {
                            //所属销售商的方案
                            $query->whereExists(function($query1) use($loginBrandId,$cityId,$loginDealerId){
                                $query1->select(DB::raw(1))
                                    ->from('designers as d')
                                    ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                    ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                                    ->where('d.organization_id',$loginDealerId)  //所属销售商
                                    ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                    ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                            });
                        });
                    });

            }else{
                return [];
            }

        }


        //（使用了此产品的方案）
        $albumIds = DB::table('album_product_ceramics as apc')
            ->where('apc.product_ceramic_id',$targetProductId)
            ->get()->pluck('album_id');

        $builder->whereIn('a.id',$albumIds);

        $builder->orderBy('sa.weight_sort','desc');
        $builder->orderBy('a.id','desc');

        $builder->skip($skip)
            ->take($take);

        $builder->select([
            'a.*','sa.web_id_code'
        ]);

        $res = $builder->get();

        $res->transform(function($v)use($loginDesigner){
            $temp = new \stdClass();

            $temp->photo = $v->photo_cover;
            $temp->count_area = $v->count_area;
            $temp->title = $v->title;
            $temp->count_visit = $v->count_visit;
            $temp->author_avatar = '';
            $temp->author_name = '';
            $temp->web_id_code = $v->web_id_code;

            $designer = Designer::find($v->designer_id);
            if($designer){
                $temp->author_avatar = $designer->detail->url_avatar;
                $temp->author_name = $designer->detail->nickname;
            }

            unset($v->id);
            return $temp;
        });

        return $res;
    }

    /**
     * 调用者：销售商详情->设计方案
     */
    public static function listDealerDetailAlbum($params,$request)
    {
        $default = [
            'targetDealerId'=>0,
            'styleId'=>0,
            'skip'=>0,
            'take'=>6
        ];

        $params = array_merge($default, $params);

        $targetDealerId = $params['targetDealerId'];
        $styleId = $params['styleId'];
        $skip = $params['skip'];
        $take = $params['take'];

        $builder = DB::table('albums as a')
            ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
            ->join('designers as d','d.id','=','a.designer_id')
            ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
            ->where('d.organization_id',$targetDealerId)  //目标销售商
            ->where('a.verify_time','<>',null) //必须是销售商已审核通过的
            ->where('a.status_dealer',Album::STATUS_DEALER_ON);  //必须是销售商展示的

        //筛选风格
        if($styleId>0){
            $style = Style::find($styleId);
            if($style) {
                $builder->where('sa.style', 'like', '%' . AlbumService::JOINER . $styleId . AlbumService::JOINER . '%');
            }
        }

        $builder->orderBy('sa.weight_sort','desc');
        $builder->orderBy('a.id','desc');

        $builder->groupBy("a.id");

        $builder_count = $builder->count();

        $builder->skip($skip)
            ->take($take);

        $builder->select([
            'a.id','a.title','a.count_area','a.designer_id','a.count_visit',
            'a.photo_cover','a.type','a.web_id_code'
        ]);

        $res = $builder->get();

        $res->transform(function($v){
            //设计师信息
            $v->designerPhoto = '';
            $v->designer = '';
            $designer = Designer::find($v->designer_id);
            if($designer){
                $designerDetail = DesignerDetail::where('designer_id',$v->designer_id)->first();
                if($designerDetail){
                    $v->designerPhoto = url($designerDetail->url_avatar);
                    $v->designer = $designerDetail->nickname;
                }
            }
            //补全封面图路径
            $v->photo_cover = url($v->photo_cover);

            unset($v->id);
            return $v;
        });

        return [
            'data'=>$res,
            'total'=>$builder_count,
            'params'=>$params
        ];
    }

}