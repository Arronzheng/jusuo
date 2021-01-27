<?php

namespace App\Http\Controllers\v1\site\product;

use App\Http\Controllers\v1\VersionController;
use App\Models\CeramicApplyCategory;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use App\Services\v1\site\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductCeramicesController_bak extends VersionController
{
    //
    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index(Request $request){
        $banners = DB::table('banners')->get();
        $brands = OrganizationBrand::where('status',OrganizationBrand::STATUS_ON)->get();
        $categories = CeramicApplyCategory::get();

        $builder = ProductCeramic::query()->where('type',ProductCeramic::TYPE_PRODUCT)->where('status',ProductCeramic::STATUS_PASS)->where('visible',ProductCeramic::VISIBLE_YES)->where('status_platform',ProductCeramic::STATUS_PLATFORM_ON);

        if($search = $request->input('search','')){
            $like = '%'.$search.'%';

            $builder->where(function($query) use ($like){
               $query->where('name','like',$like);
            });
        }

        //经营类别
        if($category = $request->input('category',[])){
            $builder->whereHas('apply_categories',function($query) use ($category){
                $query->whereIn('id',$category);
            });
        }

        //品牌
        if($brand = $request->input('brand',[])){
            $builder->whereHas('brand',function($query) use ($brand){
                $query->whereIn('id',$brand);
            });
        }

        //价格区间
        if($price = $request->input('price',[])){
            $builder->whereHas('productCeramicAuthorizePrice',function($query) use ($price){
               $query->whereIn('price',$price);
            });
        }

        if($order = $request->input('order','')){
            if(preg_match('/^(.+)_(asc|desc)$/',$order,$m)){
                if(in_array($m[1],['comples','pop','time','visit','price'])){
                    if($m[1] == 'comples'){
                        $builder->orderBy('weight_sort',$m[2]);
                    }else if($m[1] == 'pop'){
                        $builder->orderBy('count_visit',$m[2])->orderBy('count_praise',$m[2])->orderBy('count_fav',$m[2]);
                    }else if($m[1] == 'time'){
                        $builder->orderBy('created_at',$m[2]);
                    }else if($m[1] == 'visit'){
                        $builder->orderBy('count_visit',$m[2]);
                    }else if($m[1] == 'price'){
                        $builder->whereHas('productCeramicAuthorizePrice',function ($query) use ($m){
                            $query->orderBy('price',$m[2]);
                        });
                    }else{
                        $builder->orderBy('count_visit',$m[2])->orderBy('count_praise',$m[2])->orderBy('count_fav',$m[2]);
                    }
                }
            }
        }

        $products = $builder->paginate(12);

        return $this->get_view('',[
            'products' => $products,
            'beands' => $brands,
            'banners' => $banners,
            'categories' => $categories,
            'filter' => [
                'search' => $search,
                'category' => $category,
                'brand' => $brand,
                'price' => $price,
                'order' => $order,
            ]
        ]);
    }


    public function show(ProductCeramic $productCeramic){
        if($productCeramic->status != ProductCeramic::STATUS_PASS || $productCeramic->visible != ProductCeramic::VISIBLE_YES || $productCeramic->status_platform != ProductCeramic::STATUS_PLATFORM_ON){
            $this->apiSv->respFailReturn('产品状态异常');
        }

        $type_product = $productCeramic->apply_categories->product()->linit(4)->get();
        $like_product = $productCeramic::limit(4)->get();

        return $this->get_view('',['productCeramic' => $productCeramic]);
    }




}
