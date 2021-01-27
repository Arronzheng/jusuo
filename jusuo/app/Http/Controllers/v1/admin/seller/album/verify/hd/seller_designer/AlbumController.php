<?php

namespace App\Http\Controllers\v1\admin\seller\album\verify\hd\seller_designer;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Album;
use App\Models\AlbumSection;
use App\Models\Area;
use App\Models\CeramicApplyCategory;
use App\Models\CeramicColor;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\CeramicSurfaceFeature;
use App\Models\CeramicTechnologyCategory;
use App\Models\Color;
use App\Models\Designer;
use App\Models\HouseType;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\SpaceType;
use App\Models\Style;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class AlbumController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $vdata = [];
        $vdata['styles'] = Style::all();
        $vdata['colors'] = CeramicColor::all();
        $vdata['house_types'] = HouseType::all();

        return $this->get_view('v1.admin_seller.album.verify.hd.seller_designer.index',
            compact('vdata'
            ));
    }

    //查看详情
    public function detail($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;

        $data = Album::query()
            ->whereHas('designer',function($query)use($seller){
                $query->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                $query->where('organization_id',$seller->id);
            })//品牌设计师
            ->find($id);
        if(!$data){
            die('数据不存在');
        }
        $province = Area::where('id',$data->address_province_id)->first();
        $city =  Area::where('id',$data->address_city_id)->first();
        $district =  Area::where('id',$data->address_area_id)->first();
        $data->area_text = $province?$province->name:''.$city?$city->name:''.$district?$district->name:'';
        //户型
        $house_types = $data->house_types()->get()->pluck('name')->toArray();
        $data->house_type_text = implode('/',$house_types);
        //方案风格
        $styles = $data->style()->get()->pluck('name')->toArray();
        $data->style_text = implode('/',$styles);
        //空间图
        $album_sections = $data->album_sections()->get();
        $album_sections->transform(function($section){
            $content = unserialize($section->content);
            $styles = $section->styles()->get()->pluck('name')->toArray();
            $section->style_text = implode('/',$styles);
            $space_type = SpaceType::find($section->space_type_id);
            $section->space_type_text = $space_type?$space_type->name:'';
            $section->content = $content;
            return $section;
        });
        $data->album_sections = $album_sections;
        //产品清单
        $product_ceramics = $data->product_ceramics()->get();
        $product_ceramics->transform(function($v){
            $v->brand_name = $v->brand->name;
            return $v;
        });
        $data->product_ceramics = $product_ceramics;

        return $this->get_view('v1.admin_seller.album.verify.hd.seller_designer.detail',compact(
            'data'
        ));

    }

}
