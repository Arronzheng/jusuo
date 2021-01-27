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
use App\Services\v1\site\PageService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    //获取真实的登录用户（经过品牌域判断后）
    /**
     * 授权后，分以下三种情况：
        1、若该微信ID已绑定品牌内设计师账号，自动以此设计师身份完成操作
        2、若该微信ID已绑定品牌外设计师账号，则以游客身份完成操作
        3、除上述两种情况，都以游客身份完成操作
     */
    public static function getBsLoginUser($targetBrandId)
    {

        $loginUserInfo = null;

        $wechatUser = session('wechat.oauth_user.default');
        $openId = $wechatUser['id'];

        $loginDesigner = Auth::user();
        $loginGuest = Auth::guard('m_guest')->user();

        if($loginDesigner){
            //用户是设计师
            $loginBrandId = 0;
            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                $loginBrandId = $loginDesigner->organization_id;
            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                $dealer = $loginDesigner->seller;
                $loginBrandId = $dealer->p_brand_id;
            }
            //设计师是否目标品牌
            if($loginBrandId == $targetBrandId){
                //设计师是目标品牌内
                $loginUserInfo['type'] = 'designer';
                $loginUserInfo['data'] = $loginDesigner;
            }else{
                //设计师是品牌外的，需要恢复游客身份
                $guest = Guest::where('login_wx_openid',$openId)->first();
                if(!$guest){
                    $guest = GuestService::addGuest($openId);
                }
                $loginUserInfo['type'] = 'guest';
                $loginUserInfo['data'] = $guest;
            }
        }else{
            //用户不是设计师
            $loginUserInfo['type'] = 'guest';
            $loginUserInfo['data'] = $loginGuest;

        }

        return $loginUserInfo;
    }

}