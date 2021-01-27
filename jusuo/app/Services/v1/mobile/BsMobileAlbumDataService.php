<?php
/**
 * BrandScopeProductDataService
 * 异步获取product方法数据筛选（品牌域专用）
 */

namespace App\Services\v1\mobile;

use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\AlbumProductCeramic;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailDealer;
use App\Models\FavAlbum;
use App\Models\FavProduct;
use App\Models\LikeAlbum;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\Style;
use App\Services\v1\site\AlbumService;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\PageService;
use App\Services\v1\site\ProductService;
use Illuminate\Support\Facades\DB;

class BsMobileAlbumDataService
{
    /**
     * 调用者：方案列表->列表数据
     */
    public static function listAlbums($params,$request)
    {
        $default = [
            'dealerId'=>'',
            'skip'=>0,
            'take'=>6,
        ];

        $params = array_merge($default, $params);

        $take = $params['take'];

        $targetDealerId = $params['dealerId'];

        $builder = DB::table('albums as a')
            ->join('search_albums as sa', 'sa.album_id', '=', 'a.id');

        //所属销售商的设计师的方案
        $builder->whereExists(function($query1) use($targetDealerId){
            $query1->select(DB::raw(1))
                ->from('designers as d')
                ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                ->where('d.organization_id',$targetDealerId)  //所属销售商
                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
        });

        $builder->where('a.period_status',Album::PERIOD_STATUS_FINISH)
            ->where('a.visible_status',Album::VISIBLE_STATUS_ON);

        $builder->select([
            'a.photo_cover','a.web_id_code','a.title'
        ]);

        $builder->orderBy('a.weight_sort','desc');

        $res = $builder->paginate($default['take']);


        return $res;
    }

    /**
     * 调用者：方案列表->销售商全部方案列表数据
     */
    public static function listAllAlbums($dealerId)
    {
        $res = DB::table('search_albums as sa')
            ->leftJoin('albums as a','a.id','=','sa.album_id')
            ->where('sa.dealer_id',$dealerId)
            ->orderBy('a.id','desc')
            ->get(['a.photo_cover','a.web_id_code','a.title','a.designer_id']);

        return $res;
    }


    /**
     * 调用者：方案详情->相似方案
     */
    public static function listAlbumDetailSimiliar($params,$request)
    {
        $default = [
            'targetAlbumId'=>0,
            'styleIds'=>[],
            'skip'=>0,
            'take'=>6,
        ];

        $params = array_merge($default, $params);

        $take = $params['take'];
        $styleIds = $params['styleIds'];

        $targetAlbumId = $params['targetAlbumId'];
        $targetAlbum = Album::find($targetAlbumId);
        $targetDesigner = Designer::find($targetAlbum->designer_id);

        //定位城市
        $locationSession = session()->get('location_city');
        $cityId = isset($locationSession)?$locationSession:0;
        $provinceId = 0;
        if($cityId){
            $city = Area::where('level',2)->where('id',$cityId)->first();
            if($city){
                $provinceId = $city->pid;
            }
        }

        //目标设计师的所属品牌
        $targetBrandId = 0;
        if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            $targetBrandId = $targetDesigner->organization_id;

        }else if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            $targetDealer = OrganizationDealer::find($targetDesigner->organization_id);
            $targetBrandId = $targetDealer->p_brand_id;
        }

        $loginUserInfo = LoginService::getBsLoginUser($targetBrandId);

        if(!$loginUserInfo){
            return [];
        }

        if($loginUserInfo['type'] == 'guest'){
            //游客
            //服务于所在地的销售商的设计师的方案
            //20200605修正：游客可见：品牌所有设计师的方案+所在地可见的销售商的设计师的方案
            $loginBrandId = $targetBrandId;

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
            if($cityId>0){
                $builder->orWhere(function($query)use($loginBrandId,$cityId) {
                    //所在地可见销售商且被品牌显示的方案
                    $query->whereExists(function($query1) use($loginBrandId,$cityId){
                        $query1->select(DB::raw(1))
                            ->from('designers as d')
                            ->join('organization_dealers as od','od.id','=','d.organization_id')
                            ->join('detail_dealers as dd','od.id','=','dd.dealer_id')
                            ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                            ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                            ->whereRaw('(dd.area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%")')  //所在地
                            ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                            ->where('a.visible_status',Album::VISIBLE_STATUS_ON)
                            ->where('od.p_brand_id',$loginBrandId); //所在地可见的销售商需要再本品牌内
                    });
                });
            }
            /*$builder = DB::table('albums as a')
                ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where(function($query)use($targetAlbumId,$cityId) {
                    $query->whereExists(function($query1) use($targetAlbumId,$cityId){
                        $query1->select(DB::raw(1))
                            ->from('designers as d')
                            ->join('detail_dealers as dd','d.organization_id','=','dd.dealer_id')
                            ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                            ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                            ->whereRaw('(dd.area_serving_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%")')  //所在地
                            ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                            ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                    });
                });*/

        }else{
            //登录设计师的信息
            $loginDesigner = $loginUserInfo['data'];

            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //品牌设计师可见性
                //品牌所有设计师的方案+旗下销售商的所有设计师的方案
                $loginBrandId = $loginDesigner->organization_id;

                $builder = DB::table('albums as a')
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

            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //销售商设计师可见性
                //品牌所有设计师的方案+所在地可见的销售商的设计师的方案+所属销售商的设计师的方案
                $loginDealer = OrganizationDealer::find($loginDesigner->organization_id);
                $loginDealerId = $loginDealer->id;
                $loginBrand = $loginDealer->brand;
                $loginBrandId = $loginBrand->id;

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
                if($cityId>0){
                    $builder->orWhere(function($query)use($loginBrandId,$cityId) {
                        //所在地可见销售商且被品牌显示的方案
                        $query->whereExists(function($query1) use($loginBrandId,$cityId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->join('organization_dealers as od','od.id','=','d.organization_id')
                                ->join('detail_dealers as dd','od.id','=','dd.dealer_id')
                                ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                                ->whereRaw('(dd.area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%")')  //所在地
                                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                ->where('a.visible_status',Album::VISIBLE_STATUS_ON)
                                ->where('od.p_brand_id',$loginBrandId); //所在地可见的销售商需要再本品牌内
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

        $builder->where('a.period_status',Album::PERIOD_STATUS_FINISH)
            ->where('a.id','<>',$targetAlbumId)
            ->where('a.visible_status',Album::VISIBLE_STATUS_ON);

        $builder->select([
            'a.id','a.photo_cover','a.designer_id','a.web_id_code','a.title','a.count_area','a.count_visit','a.count_praise','a.count_fav'
        ]);

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

        $builder->orderBy('a.weight_sort','desc');

        $builder->limit($take);

        $res = $builder->get();

        $res->transform(function($v)use($loginUserInfo){
            $v->url_avatar = '';
            $v->nickname = '';
            $designerDetail = DesignerDetail::where('designer_id',$v->designer_id)->first();
            if($designerDetail){
                $v->url_avatar = $designerDetail->url_avatar;
                $v->nickname = $designerDetail->nickname;
            }

            $v->count_comment = AlbumComments::where('album_id',$v->id)
                ->where('status',AlbumComments::STATUS_ON)->count();

            //风格
            $style_id = DB::table('album_styles')->where('album_id',$v->id)->pluck('style_id')->toArray();
            $style = Style::whereIn('id',$style_id)->pluck('name')->toArray();
            $v->style_arr = $style;

            $v->liked = false;
            $v->collected = false;
            if($loginUserInfo['type'] == 'designer'){
                $designer = $loginUserInfo['data'];
                $liked = LikeAlbum::where([
                    'designer_id'=>$designer->id,
                    'album_id'=>$v->id,
                ])->count();
                if($liked){ $v->liked = true; }

                $collected = FavAlbum::where('designer_id',$designer->id)->where('album_id',$v->id)->first();
                if($collected){ $v->collected = true; }
            }

            unset($v->id);
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
            'targetProductId'=>0,
            'skip'=>0,
            'take'=>6,
        ];

        $params = array_merge($default, $params);

        $take = $params['take'];

        $targetProductId = $params['targetProductId'];
        $targetProduct = ProductCeramic::find($targetProductId);

        //定位城市
        $locationSession = session()->get('location_city');
        $cityId = isset($locationSession)?$locationSession:0;
        $provinceId = 0;
        if($cityId){
            $city = Area::where('level',2)->where('id',$cityId)->first();
            if($city){
                $provinceId = $city->pid;
            }
        }

        //目标产品的所属品牌
        $targetBrandId = $targetProduct->brand_id;

        $loginUserInfo = LoginService::getBsLoginUser($targetBrandId);

        if(!$loginUserInfo){
            return [];
        }

        if($loginUserInfo['type'] == 'guest'){
            //游客
            //服务于所在地的销售商的设计师的方案

            $builder = DB::table('albums as a')
                ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where(function($query)use($cityId) {
                    $query->whereExists(function($query1) use($cityId){
                        $query1->select(DB::raw(1))
                            ->from('designers as d')
                            ->join('detail_dealers as dd','d.organization_id','=','dd.dealer_id')
                            ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                            ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                            ->whereRaw('(dd.area_serving_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%")')  //所在地
                            ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                            ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                    });
                });
        }else{
            //登录设计师的信息
            $loginDesigner = $loginUserInfo['data'];

            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //品牌设计师可见性
                //品牌所有设计师的方案+旗下销售商的所有设计师的方案
                $loginBrandId = $loginDesigner->organization_id;

                $builder = DB::table('albums as a')
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

            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //销售商设计师可见性
                //品牌所有设计师的方案+所在地可见的销售商的设计师的方案+所属销售商的设计师的方案
                $loginDealer = OrganizationDealer::find($loginDesigner->organization_id);
                $loginDealerId = $loginDealer->id;
                $loginBrand = $loginDealer->brand;
                $loginBrandId = $loginBrand->id;

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
                if($cityId>0){
                    $builder->orWhere(function($query)use($loginBrandId,$cityId) {
                        //所在地可见销售商且被品牌显示的方案
                        $query->whereExists(function($query1) use($loginBrandId,$cityId){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->join('organization_dealers as od','od.id','=','d.organization_id')
                                ->join('detail_dealers as dd','od.id','=','dd.dealer_id')
                                ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)  //筛选销售商设计师
                                ->whereRaw('(dd.area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%")')  //所在地
                                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                ->where('a.visible_status',Album::VISIBLE_STATUS_ON)
                                ->where('od.p_brand_id',$loginBrandId); //所在地可见销售商需要是本品牌内的
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


        //筛选当前产品下的album
        $productAlbumIds = AlbumProductCeramic::where('product_ceramic_id',$targetProductId)
            ->get()->pluck('album_id')->toArray();

        $builder->whereIn('a.id',$productAlbumIds);

        $builder->where('a.period_status',Album::PERIOD_STATUS_FINISH)
            ->where('a.visible_status',Album::VISIBLE_STATUS_ON);


        $builder->orderBy('a.weight_sort','desc');

        $builder->limit($take);

        $builder->select([
            'a.id','a.photo_cover','a.count_area','a.title','a.count_visit','a.count_praise',
            'a.count_fav','a.web_id_code','a.designer_id'
        ]);

        $res = $builder->get();

        $res->transform(function($v)use($loginUserInfo){
            $temp = new \stdClass();

            $album = Album::find($v->id);

            $temp->photo = $v->photo_cover;
            $temp->count_area = $v->count_area;
            $temp->title = $v->title;
            $temp->count_visit = $v->count_visit;
            $temp->count_praise = $v->count_praise;
            $temp->count_fav = $v->count_fav;
            $temp->count_comment = $album->comments()->count();

            //风格
            $styles = $album->style()->get();
            $style_text = $styles->pluck('name')->toArray();
            $temp->style_arr = $style_text;

            $temp->author_avatar = '';
            $temp->author_name = '';
            $temp->author_web_id_code = '';
            $temp->web_id_code = $v->web_id_code;

            $temp->liked = false;
            $temp->collected = false;

            if($loginUserInfo['type'] == 'designer'){
                $loginDesigner = $loginUserInfo['data'];
                $liked = LikeAlbum::where([
                    'designer_id'=>$loginDesigner->id,
                    'album_id'=>$v->id,
                ])->count();
                if($liked){ $temp->liked = true; }

                $collected = FavAlbum::where('designer_id',$loginDesigner->id)->where('album_id',$v->id)->first();
                if($collected){ $temp->collected = true; }
            }


            $designer = Designer::find($v->designer_id);
            if($designer){
                $temp->author_avatar = $designer->detail->url_avatar;
                $temp->author_name = $designer->detail->nickname;
                $temp->author_web_id_code = $designer->web_id_code;
            }

            return $temp;
        });

        return $res;

    }

}