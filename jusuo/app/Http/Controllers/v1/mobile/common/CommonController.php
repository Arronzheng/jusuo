<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2019/12/11
 * Time: 15:47
 */

namespace App\Http\Controllers\v1\mobile\common;

use App\Http\Controllers\v1\admin\brand\api\BrandController;
use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Models\Album;
use App\Models\Area;
use App\Models\Banner;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailBrand;
use App\Models\DetailDealer;
use App\Models\FavDealer;
use App\Models\FavDesigner;
use App\Models\LikeAlbum;
use App\Models\FavAlbum;
use App\Models\LogBrandSiteConfig;
use App\Models\NewsBrand;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\SiteConfigPlatform;
use App\Models\Style;
use App\Services\v1\site\AlbumService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class CommonController extends VersionController{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function error($code,Request $request){
        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->goTo404($code,$__BRAND_SCOPE,'mobile');
    }

    public function get_location()
    {
        $app = app('wechat.official_account.default');
        $jssdkConfig = $app->jssdk->buildConfig(array('getLocation'), false);

        return $this->get_view('v1.mobile.common.location',compact('jssdkConfig'));

    }

    public function set_location(Request $request)
    {
        $cityName = $request->input('city','');

        if(!$cityName){
            return $this->apiSv->respFailReturn('参数缺失');
        }

        $city = Area::where('level',2)->where('name',$cityName)->first();


        if(!$city){
            return $this->apiSv->respFailReturn('找不到区域');
        }

        //写入当前城市到session
        session()->put('location_city',$city->id);

        return $this->apiSv->respDataReturn();
    }

}