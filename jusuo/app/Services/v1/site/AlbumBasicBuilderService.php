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
use App\Models\ProductCeramic;
use App\Models\SearchAlbum;
use App\Models\Style;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\LikeAlbum;
use App\Models\FavAlbum;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Runner\Exception;

class AlbumBasicBuilderService
{

    private $builder;

    public function __construct()
    {
        $this->builder = Album::query();
    }

    public function designer_visible($params)
    {
        $this->builder
            ->where(function($query) use ($params){
                //品牌设计师的所有方案
                if(isset($params['brandDesignerAlbums']) && $params['brandDesignerAlbums']===true){
                    if(!isset($params['brand_id']) || !$params['brand_id']){
                        throw new Exception("品牌id必传");
                    }
                    $query->where(function($query1)use($params){
                        $query1->whereHas('designer',function($brand_designer) use($params) {
                            //品牌设计师
                            $brand_designer->where('organization_type', Designer::ORGANIZATION_TYPE_BRAND);
                            $brand_designer->where('organization_id', $params['brand_id']);
                        });
                    });
                }

                //旗下所有销售商（且被品牌显示的）方案
                if(
                    (isset($params['brandSellerAlbums']) && $params['brandSellerAlbums']===true) ||
                    (isset($params['brandSellerAlbumsWithShow']) && $params['brandSellerAlbumsWithShow']===true)
                ){
                    if(!isset($params['brand_id']) || !$params['brand_id']){
                        throw new Exception("品牌id必传");
                    }
                    $query->orWhere(function($query1)use($params){
                        $query1->whereHas('designer',function($seller_designer) use($params) {
                            $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                            $seller_designer->whereHas('seller',function($organization)use($params){
                                $organization->where('organization_dealers.p_brand_id',$params['brand_id']);
                            });
                        });
                        if(isset($params['brandSellerAlbumsWithShow']) && $params['brandSellerAlbumsWithShow']===true){
                            $query1->where('status_brand',$params['brand_id']);
                        }
                    });
                }


                //所属销售商（且被品牌显示的）方案
                if(
                    isset($params['belongDealerAlbums']) && $params['belongDealerAlbums']===true ||
                    isset($params['belongDealerAlbumsWithShow']) && $params['belongDealerAlbumsWithShow']===true
                ){
                    if(
                        !isset($params['brand_id']) || !$params['brand_id'] ||
                        !isset($params['dealer_id']) || !$params['dealer_id']
                    ){
                        throw new Exception("品牌、销售商id必传");
                    }

                    $query->orWhere(function($query1)use($params){
                        $query1->whereHas('designer',function($seller_designer) use($params) {
                            $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                            $seller_designer->where('organization_id',$params['dealer_id']);

                        });
                        if(isset($params['belongDealerAlbumsWithShow']) && $params['belongDealerAlbumsWithShow']===true){
                            $query1->where('status_brand',$params['brand_id']);
                        }
                    });
                }
            })
            ->where('period_status',Album::PERIOD_STATUS_FINISH)
            ->where('visible_status',Album::VISIBLE_STATUS_ON);

        return $this->builder;
    }

}