<?php

namespace App\Http\Controllers\v1\mobile\mall\api;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\InfiniteTreeService;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\Area;
use App\Models\Banner;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use App\Models\HouseType;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Models\IntegralGoodsCategory;
use App\Models\IntegralLogBuy;
use App\Models\IntegralLogDesigner;
use App\Models\LikeAlbum;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use App\Models\SearchAlbum;
use App\Models\ShoppingAddress;
use App\Models\SiteConfigPlatform;
use App\Models\Space;
use App\Models\SpaceType;
use App\Models\StatisticDesigner;
use App\Models\Style;
use App\Services\v1\admin\OrganizationBrandService;
use App\Services\v1\admin\StatisticDesignerService;
use App\Services\v1\site\AlbumService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\BsAlbumDataService;
use App\Services\v1\site\BsProductDataService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\IntegralGoodService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class DetailController extends ApiController
{
    private $globalService;

    public function __construct(
        GlobalService $globalService
    ){
        $this->globalService = $globalService;
    }

    
    //详情-》获取商品详情信息
    public function good_detail($web_id_code)
    {
        $good = IntegralGood::query()
            ->select([
                'web_id_code','name','short_intro','cover','photo','integral','market_price','exchange_amount',
                'param','detail'
            ])
            ->where('web_id_code',$web_id_code)
            ->first();

        if(!$good){
            $this->respFail('信息不存在');
        }


        $good->param = \Opis\Closure\unserialize($good->param);
        $good->photo = \Opis\Closure\unserialize($good->photo);

        $this->respData($good);

    }

}
