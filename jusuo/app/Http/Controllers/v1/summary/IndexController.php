<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2019/12/11
 * Time: 15:47
 */

namespace App\Http\Controllers\v1\summary;

use App\Http\Controllers\v1\VersionController;
use App\Services\v1\site\VisibleService;

class IndexController extends VersionController {

    public function summary_single_album_visible($id){
        return VisibleService::albumSingleVisible($id);
    }

    public function summary_album_visible(){
        return VisibleService::albumBatchVisible();
    }

    public function summary_single_product_ceramic_visible($id){
        return VisibleService::productCeramicSingleVisible($id);
    }

    public function summary_product_ceramic_visible(){
        return VisibleService::productCeramicBatchVisible();
    }

    public function summary_single_designer_visible($id){
        return VisibleService::designerSingleVisible($id);
    }

    public function summary_designer_visible(){
        return VisibleService::designerBatchVisible();
    }

    public function summary_single_dealer_visible($id){
        return VisibleService::dealerSingleVisible($id);
    }

    public function summary_dealer_visible(){
        return VisibleService::dealerBatchVisible();
    }

}