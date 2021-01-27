<?php

namespace App\Services\v1\site;

use App\Http\Services\common\StrService;
use App\Models\Album;
use App\Models\AlbumStyle;
use App\Models\AlbumSpaceType;
use App\Models\AlbumHouseType;
use App\Models\AlbumProductCeramic;
use App\Models\DetailDealer;
use App\Models\FavDealer;
use App\Models\FavDesigner;
use App\Models\FavProduct;
use App\Models\HouseType;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\SearchAlbum;
use App\Models\Style;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\LikeAlbum;
use App\Models\FavAlbum;
use App\Models\VisitAlbum;
use App\Models\VisitDealer;
use App\Models\VisitDesigner;
use App\Models\VisitProduct;
use Illuminate\Support\Facades\Auth;

class VisibleService
{
    const MAX_ID=9999999;

    public static function albumSingleVisible($albumId){
        $album = Album::find($albumId);
        if(!$album)
            return false;
        $designer = Designer::find($album->designer_id);
        if(!$designer)
            return false;

        $brand_id=0;
        $dealer_id=0;
        $area_serving_cities='';
        $area_visible_cities='';

        switch($designer->organization_type) {
            case Designer::ORGANIZATION_TYPE_BRAND:
                $organization = OrganizationBrand::find($designer->organization_id);
                if (!$organization)
                    return false;
                $brand_id = (($album->status_brand>0)&&($album->visible_status==Album::VISIBLE_STATUS_ON)&&($album->status==Album::STATUS_PASS))?$organization->id:0;
                break;
            case Designer::ORGANIZATION_TYPE_SELLER:
                $organization = OrganizationDealer::find($designer->organization_id);
                if (!$organization)
                    return false;
                /*while ($organization->p_dealer_id <> 0) {
                    $organization = OrganizationDealer::find($organization->p_dealer_id);
                    if (!$organization) {
                        return false;
                        break;
                    }
                }*/
                $detailDealer = DetailDealer::where('dealer_id', $organization->id)->first();

                if (!$detailDealer)
                    return false;
                $brand_id = (($album->status_brand>0)&&($album->status==Album::STATUS_PASS))?$organization->p_brand_id:0;
                $dealer_id = (($album->status_brand>0)&&($album->status==Album::STATUS_PASS))?$organization->id:0;
                $area_serving_cities = $detailDealer->area_serving_city;
                $area_visible_cities = $detailDealer->area_visible_city;
                break;
            default:
                break;
        }

        SearchAlbum::where('album_id',$albumId)->update([
            'brand_id'=>$brand_id,
            'dealer_id'=>$dealer_id,
            'area_serving_cities'=>$area_serving_cities,
            'area_visible_cities'=>$area_visible_cities,
        ]);
        return true;
    }

    public static function albumBatchVisible($start=1,$end=VisibleService::MAX_ID){
        $max=Album::max('id');
        $end=($end>$max?$max:$end);
        $count=0;
        for($i=$start;$i<=$end;$i++){
            if(self::albumSingleVisible($i)){
                $count++;
            }
        }
        return $count;
    }

    public static function productCeramicSingleVisible($id){
        $productAuthorization = ProductCeramicAuthorization::find($id);
        if(!$productAuthorization)
            return false;
        $organization = OrganizationDealer::find($productAuthorization->dealer_id);
        if (!$organization)
            return false;
        while ($organization->p_dealer_id <> 0) {
            $organization = OrganizationDealer::find($organization->p_dealer_id);
            if (!$organization) {
                return false;
                break;
            }
        }
        $detailDealer = DetailDealer::where('dealer_id', $organization->id)->first();
        if (!$detailDealer)
            return false;
        $brand_id = $organization->p_brand_id;
        $dealer_id = $organization->id;
        $area_serving_cities = $detailDealer->area_serving_city;
        $area_visible_cities = $detailDealer->area_visible_city;

        ProductCeramicAuthorization::where('id',$id)->update([
            'brand_id'=>$brand_id,
            'dealer_id'=>$dealer_id,
            'area_serving_cities'=>$area_serving_cities,
            'area_visible_cities'=>$area_visible_cities,
        ]);
        return true;
    }

    public static function productCeramicBatchVisible($start=1,$end=VisibleService::MAX_ID){
        $max=ProductCeramicAuthorization::max('id');
        $end=($end>$max?$max:$end);
        $count=0;
        for($i=$start;$i<=$end;$i++){
            if(self::productCeramicSingleVisible($i)){
                $count++;
            }
        }
        return $count;
    }

    public static function designerSingleVisible($id){
        $designer = Designer::find($id);
        if(!$designer)
            return false;

        $brand_id=0;
        $dealer_id=0;
        $area_serving_cities='';
        $area_visible_cities='';

        switch($designer->organization_type) {
            case Designer::ORGANIZATION_TYPE_BRAND:
                $organization = OrganizationBrand::find($designer->organization_id);
                if (!$organization)
                    return false;
                $brand_id = $organization->id;
                break;
            case Designer::ORGANIZATION_TYPE_SELLER:
                $organization = OrganizationDealer::find($designer->organization_id);
                if (!$organization)
                    return false;
                /*while ($organization->p_dealer_id <> 0) {
                    $organization = OrganizationDealer::find($organization->p_dealer_id);
                    if (!$organization) {
                        return false;
                        break;
                    }
                }*/
                $detailDealer = DetailDealer::where('dealer_id', $organization->id)->first();

                if (!$detailDealer)
                    return false;
                $brand_id = $organization->p_brand_id;
                $dealer_id = $organization->id;
                $area_serving_cities = $detailDealer->area_serving_city;
                $area_visible_cities = $detailDealer->area_visible_city;
                break;
            default:
                break;
        }

        DesignerDetail::where('designer_id',$id)->update([
            'brand_id'=>$brand_id,
            'dealer_id'=>$dealer_id,
            'area_serving_cities'=>$area_serving_cities,
            'area_visible_cities'=>$area_visible_cities,
        ]);
        return true;
    }

    public static function designerBatchVisible($start=1,$end=VisibleService::MAX_ID){
        $max=Designer::max('id');
        $end=($end>$max?$max:$end);
        $count=0;
        for($i=$start;$i<=$end;$i++){
            if(self::designerSingleVisible($i)){
                $count++;
            }
        }
        return $count;
    }

    public static function dealerSingleVisible($id){
        $organization = OrganizationDealer::find($id);
        if (!$organization)
            return false;
        while ($organization->p_dealer_id <> 0) {
            $organization = OrganizationDealer::find($organization->p_dealer_id);
            if (!$organization) {
                return false;
                break;
            }
        }
        $detailDealer = DetailDealer::where('dealer_id', $organization->id)->first();

        if (!$detailDealer)
            return false;
        $brand_id = $organization->p_brand_id;

        DetailDealer::where('dealer_id',$id)->update([
            'brand_id'=>$brand_id
        ]);
        return true;
    }

    public static function dealerBatchVisible($start=1,$end=VisibleService::MAX_ID){
        $max=OrganizationDealer::max('id');
        $end=($end>$max?$max:$end);
        $count=0;
        for($i=$start;$i<=$end;$i++){
            if(self::dealerSingleVisible($i)){
                $count++;
            }
        }
        return $count;
    }

}