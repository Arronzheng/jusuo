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
use App\Models\IntegralGood;
use App\Models\IntegralGoodAuthorization;
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

class IntegralGoodService
{
    const JOINER = '|';

    //获取正常出售的积分商品entry
    public static function getForSaleEntry()
    {
        $entry = IntegralGood::query()
            ->where('status',IntegralGood::STATUS_ON)
            ->where('is_delete',IntegralGood::IS_DELETE_NO);

        return $entry;

    }

    public static function getOrgBrandGoodIds($brand_id)
    {
        $good_ids = [];
        //平台授权给品牌的商品
        $platform_good_ids = IntegralGoodAuthorization::where('brand_id',$brand_id)
            ->where('visible_status',IntegralGoodAuthorization::VISIBLE_STATUS_ON)
            ->where('status',IntegralGoodAuthorization::STATUS_BRAND_USED)
            ->get()->pluck('good_id')->toArray();
        //品牌自建商品
        $brand_good_ids = IntegralGood::where('brand_id',$brand_id)
            ->where('status',IntegralGood::STATUS_ON)
            ->get()->pluck('id')->toArray();
        $merge_ids = array_merge($platform_good_ids,$brand_good_ids);
        if(count($merge_ids)>0){
            $good_ids = array_unique($merge_ids);
        }

        return $good_ids;
    }
}