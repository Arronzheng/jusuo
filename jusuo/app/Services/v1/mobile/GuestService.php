<?php

namespace App\Services\v1\mobile;

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
use App\Models\Guest;
use App\Models\HouseType;
use App\Models\ProductCeramic;
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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GuestService
{

    //添加游客
    public static function addGuest($openId)
    {
        $guest = new Guest();
        $guest->login_wx_openid = $openId;
        $id_code = StrService::str_random_field_value('albums','web_id_code',16,10);
        if($id_code['tryLeft']>0){
            $guest->web_id_code = $id_code['string'];
        }
        $guest->web_id_code = $openId;
        $guest->status = Guest::STATUS_ON;
        $guest->save();

        return $guest;
    }



}