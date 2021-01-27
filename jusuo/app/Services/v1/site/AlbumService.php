<?php

namespace App\Services\v1\site;

use App\Http\Services\common\StrService;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\AlbumStyle;
use App\Models\AlbumSpaceType;
use App\Models\AlbumHouseType;
use App\Models\AlbumProductCeramic;
use App\Models\HouseType;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\SearchAlbum;
use App\Models\Style;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\LikeAlbum;
use App\Models\FavAlbum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlbumService
{
    const JOINER = '|';

    //获取album所属品牌
    public static function getAlbumOrganization($targetAlbumId)
    {
        $result = [];

        $targetAlbum = Album::find($targetAlbumId);
        $targetDesigner = Designer::find($targetAlbum->designer_id);
        $targetDesignerId = $targetDesigner->id;
        $targetBrandId = 0;
        $targetDealerId = 0;
        if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            $targetBrandId = $targetDesigner->organization_id;

        }else if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            $targetDealer = OrganizationDealer::find($targetDesigner->organization_id);
            $targetDealerId = $targetDealer->id;
            $targetBrandId = $targetDealer->p_brand_id;
        }

        $result['designer_id'] = $targetDesignerId;
        $result['brand_id'] = $targetBrandId;
        $result['dealer_id'] = $targetDealerId;

        return $result;

    }

    public static function getAlbumByStyle($brand_scope, $styleId, $isOrderByWeight=false, $isTopPlatform=false, $isTopBrand=false, $skip=0, $take=5){
        $style = Style::find($styleId);
        $brandId = $brand_scope;
        if($brandId>0){
            $album = \DB::table('albums as a')
                ->leftJoin('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where([
                    'a.status_brand'=>$brandId,
                    'a.status'=>Album::STATUS_PASS,
                    'a.status_visible'=>Album::VISIBLE_STATUS_ON,
                ])
                ->whereNotNull('a.id');
        }
        else{
            $album = \DB::table('search_albums as sa')->where('id','>',0);
        }
        if($style) {
            $album->where('sa.style', 'like', '%' . AlbumService::JOINER . $styleId . AlbumService::JOINER . '%');
        }
        if($isTopPlatform){
            $album->where('sa.top_status_platform',Album::TOP_PLATFORM_ON);
        }
        if($isTopBrand){
            $album->where('sa.top_status_brand',Album::TOP_BRAND_ON);
        }
        if($isOrderByWeight){
            $album->orderBy('sa.weight_sort','desc');
        }
        $album->orderBy('sa.album_id','desc');
        $album->skip($skip)
            ->take($take);
        $res = [];
        foreach($album->pluck('sa.web_id_code','sa.album_id')->all() as $album_id=>$web_id_code){
            if(!$album_id)
                continue;
            $v = Album::where('id',$album_id)
                ->get(['id','title','count_area','count_visit','count_praise',
                    'count_fav','photo_cover','designer_id']);
            if(!$v)
                continue;
            $v = $v[0];
            $v->web_id_code = $web_id_code;
            $designer = Designer::find($v->designer_id);
            if($designer){
                $designerDetail = DesignerDetail::where('designer_id',$v->designer_id)->first();
                if($designerDetail){
                    $v->designerPhoto = url($designerDetail->url_avatar);
                    $v->designer = $designerDetail->nickname;
                    $v->identity = $designerDetail->approve_realname==DesignerDetail::APPROVE_REALNAME_YES?true:false;
                    $v->hot = $designerDetail->top_status==DesignerDetail::TOP_STATUS_YES?true:false;
                }
            }
            $likeAlbum = false;
            $favAlbum = false;
            $user = Auth::user();
            if($user) {
                $userId = $user->id;
                if(LikeAlbum::where(['designer_id'=>$userId,'album_id'=>$v->id])->count()) {
                    $likeAlbum = true;
                }
                if(FavAlbum::where(['designer_id'=>$userId,'album_id'=>$v->id])->count()) {
                    $favAlbum = true;
                }
            }
            $v->liked = $likeAlbum;
            $v->like = LikeAlbum::where(['album_id'=>$v->id])->count();
            $v->collected = $favAlbum;
            $v->collect = FavAlbum::where(['album_id'=>$v->id])->count();
            $v->panorama = $v->type==Album::TYPE_KUJIALE_SOURCE?true:false;
            $v->photo_cover = url($v->photo_cover);
            $res[] = $v;
        }
        return [
            'album'=>$res,
            'brand'=>$brandId
        ];
    }

    public static function getAlbum($params){
        //调用方法的设计师model
        $requestDesigner = isset($params['requestDesigner'])?$params['requestDesigner']:null;
        $brand_scope = isset($params['brand_scope'])?$params['brand_scope']:0;
        $organizationType = isset($params['organizationType'])?$params['organizationType']:0;
        $organizationId = isset($params['organizationId'])?$params['organizationId']:0;
        $designerId = isset($params['designerId'])?$params['designerId']:0;
        $styleId = isset($params['styleId'])?$params['styleId']:0;
        $isOrderByWeight = isset($params['isOrderByWeight'])?$params['isOrderByWeight']:true;
        $isTopPlatform = isset($params['isTopPlatform'])?$params['isTopPlatform']:false;
        $isTopBrand = isset($params['isTopBrand'])?$params['isTopBrand']:false;
        $isTopDesigner = isset($params['isTopDesigner'])?$params['isTopDesigner']:false;
        $skip = isset($params['skip'])?$params['skip']:0;
        $take = isset($params['take'])?$params['take']:6;
        $needPraiseFav = isset($params['needPraiseFav'])?$params['needPraiseFav']:false;
        $needTypeStyle = isset($params['needTypeStyle'])?$params['needTypeStyle']:false;
        $isStatusPass = isset($params['isStatusPass'])?$params['isStatusPass']:false;
        $isRepresent = isset($params['isRepresent'])?$params['isRepresent']:false;
        $needCommentCount = isset($params['needCommentCount'])?$params['needCommentCount']:false;
        //筛选旗下所有销售商的方案时，是否筛选被品牌显示的方案，默认是true
        $brandScopeSellerShow = isset($params['brandScopeSellerShow'])?$params['brandScopeSellerShow']:true;

        //优先按设计师查询
        if($designerId>0){
            $designer = Designer::find($designerId);
            if($designer->status<>Designer::STATUS_ON){
                return [
                    'data'=>[],
                    'param'=>$params,
                ];
            }
            $album = \DB::table('albums as a')
                ->leftJoin('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where([
                    'a.designer_id'=>$designerId,
                    'a.visible_status'=>Album::VISIBLE_STATUS_ON,
                    'a.period_status'=>Album::PERIOD_STATUS_FINISH,
                ]);
        }
        else if($organizationId>0){
            if($organizationType==Designer::ORGANIZATION_TYPE_SELLER){
                $tableName = 'organization_dealers';
                $album = \DB::table($tableName.' as b')
                    ->leftJoin('designers as d', 'd.organization_id', '=', 'b.id')
                    ->leftJoin('albums as a', 'a.designer_id','=','d.id')
                    ->leftJoin('search_albums as sa', 'sa.album_id', '=', 'a.id')
                    ->where([
                        'd.organization_type'=>$organizationType,
                        'd.organization_id'=>$organizationId,
                        'a.visible_status'=>Album::VISIBLE_STATUS_ON,
                        'a.period_status'=>Album::PERIOD_STATUS_FINISH,
                    ])
                    ->whereNotNull('a.id');
            }
            else{
                $album = \DB::table('albums as a')
                    ->leftJoin('search_albums as sa', 'sa.album_id', '=', 'a.id')
                    ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                    ->where('a.visible_status',Album::VISIBLE_STATUS_ON)
                    ->where('a.status_brand',$organizationId);//必须是申请品牌展示已通过的

            }
        }
        //品牌域内
        else if($brand_scope>0){
            if($requestDesigner==null){
                return [
                    'data'=>[],
                    'param'=>$params,
                ];
            }
            if($requestDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
                //品牌设计师可见性
                //品牌设计师的所有方案+旗下所有销售商且被品牌显示的方案
                $album = \DB::table('albums as a')
                    ->join('search_albums as sa', 'sa.album_id', '=', 'a.id')
                    ->where(function($query) use($brand_scope){
                        $query->whereExists(function($query1) use($brand_scope){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->whereRaw('d.id = a.designer_id')  //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
                                ->where('d.organization_id',$brand_scope)
                                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                ->where('a.visible_status',Album::VISIBLE_STATUS_ON);

                        });
                    })
                    ->orWhere(function($query) use($brand_scope,$brandScopeSellerShow){
                        $query->whereExists(function($query1) use($brand_scope,$brandScopeSellerShow){
                            $query1->select(DB::raw(1))
                                ->from('designers as d')
                                ->join('organization_dealers as od','d.organization_id','=','od.id')
                                ->whereRaw('d.id = a.designer_id')   //与外层表连接必须用whereRaw
                                ->where('d.organization_type',Designer::ORGANIZATION_TYPE_SELLER)
                                ->where('od.p_brand_id',$brand_scope)
                                ->where('a.status',Album::STATUS_PASS) //必须是已审核通过的
                                ->where('a.visible_status',Album::VISIBLE_STATUS_ON);
                                if($brandScopeSellerShow){
                                    $query1->where('a.status_brand',$brand_scope);
                                }

                        });
                    });

            }else if($requestDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
                //销售商直属设计师可见性

            }else{
                $album = \DB::table('albums as a')
                    ->where('id',0);
            }

        }
        else{
            $album = \DB::table('albums as a')
                ->leftJoin('search_albums as sa', 'sa.album_id', '=', 'a.id')
                ->where([
                    'a.visible_status'=>Album::VISIBLE_STATUS_ON,
                    'a.status'=>Album::STATUS_PASS,
                ]);
        }
        //如果需要检查$brand_scope，在这里进行
        //目前暂时忽略
        $style = Style::find($styleId);
        if($style) {
            $album->where('sa.style', 'like', '%' . AlbumService::JOINER . $styleId . AlbumService::JOINER . '%');
        }
        if($isTopPlatform){
            $album->where('sa.top_status_platform',Album::TOP_PLATFORM_ON);
        }
        //是否代表作
        if($isRepresent){
            $album->where('a.is_representative_work',$isRepresent);
        }
        if($isTopBrand){
            $album->where(function($query){
                $query->where('sa.top_status_brand',Album::TOP_BRAND_ON);
                $query->orWhere('a.top_status_brand',Album::TOP_BRAND_ON);
            });
        }
        if($isTopDesigner){
            $album->where('a.top_status_designer',Album::TOP_DESIGNER_ON);
        }
        if($isOrderByWeight){
            $album->orderBy('sa.weight_sort','desc');
        }
        if($isStatusPass){
            $album->where('status',Album::STATUS_PASS);
        }

        $album->select(['a.*','a.id as album_id']);

        $album->orderBy('sa.album_id','desc');

        $album_count = $album->count();

        $album->skip($skip)
            ->take($take);

        $res = [];

        $all = $album->get()->pluck('web_id_code','album_id');

        foreach($all as $album_id=>$web_id_code){
            if(!$album_id)
                continue;
            $v = Album::where('id',$album_id)
                ->get(['id','title','count_area','count_visit','count_praise',
                    'count_fav','photo_cover','designer_id','description_design']);
            if(!$v)
                continue;
            $v = $v[0];
            $designer = Designer::find($v->designer_id);
            if($designer){
                $designerDetail = DesignerDetail::where('designer_id',$v->designer_id)->first();
                if($designerDetail){
                    $v->designerPhoto = url($designerDetail->url_avatar);
                    $v->designer = $designerDetail->nickname;
                    //$v->identity = $designerDetail->approve_realname==DesignerDetail::APPROVE_REALNAME_YES?true:false;
                    //$v->hot = $designerDetail->top_status==DesignerDetail::TOP_STATUS_YES?true:false;
                }
            }
            //要求查询收藏和点赞
            if($needPraiseFav) {
                $likeAlbum = false;
                $favAlbum = false;
                $user = Auth::user();
                if ($user) {
                    $userId = $user->id;
                    if (LikeAlbum::where(['designer_id' => $userId, 'album_id' => $v->id])->count()) {
                        $likeAlbum = true;
                    }
                    if (FavAlbum::where(['designer_id' => $userId, 'album_id' => $v->id])->count()) {
                        $favAlbum = true;
                    }
                }
                $v->liked = $likeAlbum;
                $v->like = LikeAlbum::where(['album_id' => $v->id])->count();
                $v->collected = $favAlbum;
                $v->collect = FavAlbum::where(['album_id' => $v->id])->count();
            }
            if($needCommentCount){
                $v->count_comment = AlbumComments::where(['album_id' => $v->id])->count();
            }
            //要求查询户型、风格（只显示一个）
            if($needTypeStyle){
                $v->style = AlbumService::getAlbumStyle($v->id);
                $v->house_type = AlbumService::getAlbumHouseType($v->id);
            }
            $v->panorama = $v->type==Album::TYPE_KUJIALE_SOURCE?true:false;
            $v->photo_cover = url($v->photo_cover);
            $v->web_id_code = $web_id_code;
            $res[] = $v;
        }
        return [
            'data'=>$res,
            'total'=>$album_count,
            'params'=>$params
        ];
    }

    public static function getAlbumStyle($albumId, $stopAt=1){
        $rec = SearchAlbum::where(['album_id'=>$albumId])->first();
        $res = [];
        $count = 0;
        if($rec&&$rec->style){
            $recArray = explode(AlbumService::JOINER,$rec->style);
            foreach($recArray as $v){
                if($v<>''){
                    $get = Style::find($v);
                    if($get){
                        $res[] = $get->name;
                        $count++;
                        if($count>=$stopAt){
                            break;
                        }
                    }
                }
            }
        }
        return $res;
    }

    public static function getAlbumHouseType($albumId, $stopAt=1){
        $rec = SearchAlbum::where(['album_id'=>$albumId])->first();
        $res = [];
        $count = 0;
        if($rec&&$rec->house_type){
            $recArray = explode(AlbumService::JOINER,$rec->house_type);
            foreach($recArray as $v){
                if($v<>''){
                    $get = HouseType::find($v);
                    if($get){
                        $res[] = $get->name;
                        $count++;
                        if($count>=$stopAt){
                            break;
                        }
                    }
                }
            }
        }
        return $res;
    }

    public static function addWebIdCode()
    {
        $sa = SearchAlbum::pluck('web_id_code', 'album_id');
        $count = 0;
        foreach ($sa as $k=>$v) {
            if($v<>'') {
                Album::where('id', $k)->update(['web_id_code' => $v]);
                $count++;
            }
        }
        return $count.'/'.count($sa);
    }

    public static function addToSearchSingle($albumId)
    {
        $v = Album::find($albumId);
        if(!$v){
            return;
        }
        $albumId = $v->id;
        $designerId = $v->designer_id;
        $designerDetail = DesignerDetail::where('designer_id',$designerId)->first();
        if(!$designerDetail){
            return;
        }
        $searchAlbum = SearchAlbum::where('album_id',$albumId)->first();
        if(!$searchAlbum){
            $searchAlbum = new SearchAlbum();
        }
        $searchAlbum->album_id = $albumId;
        $searchAlbum->web_id_code = $v->web_id_code;
        $searchAlbum->title = $v->title;
        $searchAlbum->weight_sort = $v->weight_sort;
        $searchAlbum->top_status_platform = $v->top_status_platform;
        $searchAlbum->top_status_brand = $v->top_status_brand;
        $searchAlbum->style = AlbumService::getAlbumStyleSearch($albumId);
        $searchAlbum->space_type = AlbumService::getAlbumSpaceTypeSearch($albumId);
        $searchAlbum->house_type = AlbumService::getAlbumHouseTypeSearch($albumId);
        $searchAlbum->product_ceramic = AlbumService::getAlbumProductCeramicSearch($albumId);

        $searchAlbum->brand_id = $designerDetail->brand_id;
        $searchAlbum->dealer_id = $designerDetail->dealer_id;
        $searchAlbum->area_serving_cities = $designerDetail->area_serving_cities;
        $searchAlbum->area_visible_cities = $designerDetail->area_visible_cities;

        $searchAlbum->save();
    }

    public static function addToSearch()
    {
        $album = Album::where([
            'status_search'=>Album::STATUS_SEARCH_OFF
            /*'visible_status'=>Album::VISIBLE_STATUS_ON*/
        ])->get(['id','web_id_code','title','weight_sort','status','visible_status','status_brand','top_status_platform','top_status_brand','status_search']);
        $count = 0;
        if($album){
            foreach ($album as $v){
                $albumId = $v->id;
                $searchAlbum = SearchAlbum::where('album_id',$albumId)->count();
                Album::where('id',$albumId)->update(['status_search' => Album::STATUS_SEARCH_ON]);
                if($searchAlbum){
                    continue;
                }
                $searchAlbum = new SearchAlbum();
                $searchAlbum->album_id = $albumId;
                $searchAlbum->web_id_code = $v->web_id_code;
                $searchAlbum->brand_id = ($v->status_brand>0)?$v->status_brand:0;
                $searchAlbum->title = $v->title;
                $searchAlbum->weight_sort = $v->weight_sort;
                $searchAlbum->top_status_platform = $v->top_status_platform;
                $searchAlbum->top_status_brand = $v->top_status_brand;
                $searchAlbum->style = AlbumService::getAlbumStyleSearch($albumId);
                $searchAlbum->space_type = AlbumService::getAlbumSpaceTypeSearch($albumId);
                $searchAlbum->house_type = AlbumService::getAlbumHouseTypeSearch($albumId);
                $searchAlbum->product_ceramic = AlbumService::getAlbumProductCeramicSearch($albumId);
                $searchAlbum->save();
                $count++;
            }
        }
        //$web_id_code_count = StrService::str_table_field_unique('search_albums');

        //return $count.' '.$web_id_code_count;
        return $count;
    }

    public static function getAlbumStyleSearch($albumId){
        $albumStyle = AlbumStyle::where('album_id',$albumId)->pluck('style_id')->all();
        return AlbumService::JOINER.implode(AlbumService::JOINER,array_unique($albumStyle)).AlbumService::JOINER;
    }

    public static function getAlbumSpaceTypeSearch($albumId){
        $albumSpaceType = AlbumSpaceType::where('album_id',$albumId)->pluck('space_type_id')->all();
        return AlbumService::JOINER.implode(AlbumService::JOINER,array_unique($albumSpaceType)).AlbumService::JOINER;
    }

    public static function getAlbumHouseTypeSearch($albumId){
        $albumHouseType = AlbumHouseType::where('album_id',$albumId)->pluck('house_type_id')->all();
        return AlbumService::JOINER.implode(AlbumService::JOINER,array_unique($albumHouseType)).AlbumService::JOINER;
    }

    public static function getAlbumProductCeramicSearch($albumId){
        $albumProductCeramic = AlbumProductCeramic::where('album_id',$albumId)->pluck('product_ceramic_id')->all();
        $albumProductCeramic = array_unique($albumProductCeramic);
        $productCeramicName = [];
        foreach($albumProductCeramic as $v){
            $productCeramic = ProductCeramic::find($v);
            if($productCeramic){
                $productCeramicName[] = $productCeramic->name;
            }
        }
        return AlbumService::JOINER.implode(AlbumService::JOINER,$productCeramicName).AlbumService::JOINER;
    }

}