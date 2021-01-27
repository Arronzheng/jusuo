<?php

namespace App\Http\Controllers\v1\site\product;

use App\Http\Controllers\v1\VersionController;
use App\Models\CeramicApplyCategory;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\BsProductPageAccessService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductCeramicsController extends VersionController
{
    //
    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index(Request $request){
        //页面访问可见性
        $pageVisible = BsProductPageAccessService::productIndex([
            'loginDesigner' => Auth::user(),
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id')
        ],$request);

        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code']);
        }

        $keyword = $request->input('k','');

        return $this->get_view('v1.site.product.index',compact( 'keyword'));

    }


    public function detail($id,Request $request)
    {
        $product = ProductCeramic::where('web_id_code',$id)->first();

        if(!$product){
            return $this->goTo404(PageService::ErrorNoResult);
        }

        if(
            $product->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->goTo404(PageService::ErrorNoResult);
        }

        //页面访问可见性
        $pageVisible = BsProductPageAccessService::productDetail([
            'loginDesigner' => Auth::user(),
            'targetProductId' => $product->id,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id')
        ],$request);

        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code']);
        }


        OpService::visitProduct($product->id,$request);

        return $this->get_view('v1.site.product.detail');
    }

    //预览详情页
    public function detail_preview($id,Request $request)
    {

        $product = ProductCeramic::where('web_id_code',$id)->first();

        if(!$product){
            return $this->goTo404(PageService::ErrorNoResult);
        }

        if(
            $product->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->goTo404(PageService::ErrorNoResult);
        }

        $preview_brand_id = session()->get('preview_brand_id');

        $targetBrandId = $product->brand_id;
        if($preview_brand_id != $targetBrandId){
            return $this->goTo404(PageService::ErrorNoAuthority);
        }

        $is_preview = true;

        return $this->get_view('v1.site.product.detail',compact('is_preview'));


    }




}
