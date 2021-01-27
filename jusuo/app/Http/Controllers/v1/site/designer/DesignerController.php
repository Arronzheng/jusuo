<?php

namespace App\Http\Controllers\v1\site\designer;

use App\Http\Controllers\v1\VersionController;
use App\Models\CeramicApplyCategory;
use App\Models\Designer;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\BsDesignerPageAccessService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DesignerController extends VersionController
{
    //
    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index(Request $request){
        $loginDesigner = Auth::user();

        //页面可见性
        $pageVisible = BsDesignerPageAccessService::designerIndex([
            'loginDesigner' => $loginDesigner,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id')
        ],$request);

        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code']);
        }

        $keyword = $request->input('k','');

        return $this->get_view('v1.site.designer.index',compact('keyword'));

    }


    public function detail($web_id_code,Request $request)
    {
        $loginDesigner = Auth::user();

        $designer = Designer::where('web_id_code',$web_id_code)->first();

        if(!$designer){
            return redirect('/')->withErrors(['设计师不存在']);
        }

        if(
            $designer->status != Designer::STATUS_ON
        ){
            return redirect('/')->withErrors(['设计师状态异常']);
        }

        //页面可见性
        $pageVisible = BsDesignerPageAccessService::designerDetail([
            'loginDesigner' => $loginDesigner,
            'targetDesignerId' => $designer->id,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id')
        ],$request);

        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code']);
        }

        OpService::visitDesigner($designer->id,$request);

        $designerId = $web_id_code;

        return $this->get_view('v1.site.designer.detail',compact('designerId'));
    }




}
