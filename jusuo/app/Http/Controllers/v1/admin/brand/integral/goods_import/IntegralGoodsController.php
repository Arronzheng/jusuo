<?php

namespace App\Http\Controllers\v1\admin\brand\integral\goods_import;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\InfiniteTreeService;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Models\IntegralGoodsCategory;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class IntegralGoodsController extends VersionController
{
    private $infiniteTreeService;

    function __construct(InfiniteTreeService $infiniteTreeService )
    {
        $this->infiniteTreeService = $infiniteTreeService;
    }

    public function index(Request $request)
    {
        $brands = IntegralBrand::query()->get();

        $categories = IntegralGoodsCategory::query()->get();
        $categories = $this->infiniteTreeService->getFlatTree($categories,'pid');
        $categories = collect($categories)->transform(function($v){
            $v->name = str_repeat('|-- ',$v->level).$v->name;
            return $v;
        });

        return $this->get_view('v1.admin_brand.integral.goods_import.index',compact('brands','categories'));
    }


}
