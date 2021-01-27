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
use App\Models\GuestFavAlbum;
use App\Models\GuestFavDealer;
use App\Models\GuestFavDesigner;
use App\Models\GuestFavProduct;
use App\Models\GuestLikeAlbum;
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

class OpService
{

    public static function visitAlbum($albumId, $request){
        $user = Auth::user();
        $ip = '';
        $designerId = 0;
        if(!$user) {
            $ip = $request->getClientIp();
            $rec = VisitAlbum::where([
                'ip'=>$ip,
                'album_id'=>$albumId,
            ])->orderBy('id','desc')->first();
        }
        else{
            $designerId = $user->id;
            $rec = VisitAlbum::where([
                'designer_id'=>$designerId,
                'album_id'=>$albumId,
            ])->orderBy('id','desc')->first();
        }
        if($rec){
            return false;
        }
        $rec = new VisitAlbum();
        $rec->designer_id = $designerId;
        $rec->ip = $ip;
        $rec->album_id = $albumId;
        $rec->save();
        $count = VisitAlbum::where([
            'album_id'=>$albumId,
        ])->count();
        Album::where('id',$albumId)->update(['count_visit'=>$count]);
        return true;
    }

    public static function visitDesigner($targetDesignerId, $request){
        $user = Auth::user();
        $ip = '';
        $designerId = 0;
        if(!$user) {
            $ip = $request->getClientIp();
            $rec = VisitDesigner::where([
                'ip'=>$ip,
                'target_designer_id'=>$targetDesignerId,
            ])->orderBy('id','desc')->first();
        }
        else{
            $designerId = $user->id;
            $rec = VisitDesigner::where([
                'designer_id'=>$designerId,
                'target_designer_id'=>$targetDesignerId,
            ])->orderBy('id','desc')->first();
        }
        if($rec){
            return false;
        }
        $rec = new VisitDesigner();
        $rec->designer_id = $designerId;
        $rec->ip = $ip;
        $rec->target_designer_id = $targetDesignerId;
        $rec->save();
        $count = VisitDesigner::where([
            'target_designer_id'=>$targetDesignerId,
        ])->count();
        DesignerDetail::where('designer_id',$targetDesignerId)->update(['count_visit'=>$count]);
        return true;
    }

    public static function visitProduct($productId, $request){
        $user = Auth::user();
        $ip = '';
        $designerId = 0;
        if(!$user) {
            $ip = $request->getClientIp();
            $rec = VisitProduct::where([
                'ip'=>$ip,
                'product_id'=>$productId,
            ])->orderBy('id','desc')->first();
        }
        else{
            $designerId = $user->id;
            $rec = VisitProduct::where([
                'designer_id'=>$designerId,
                'product_id'=>$productId,
            ])->orderBy('id','desc')->first();
        }
        if($rec){
            return false;
        }
        $rec = new VisitProduct();
        $rec->designer_id = $designerId;
        $rec->ip = $ip;
        $rec->product_id = $productId;
        $rec->save();
        $count = VisitProduct::where([
            'product_id'=>$productId,
        ])->count();
        ProductCeramic::where('id',$productId)->update(['count_visit'=>$count]);
        return true;
    }

    public static function visitDealer($dealerId, $request){
        $user = Auth::user();
        $ip = '';
        $designerId = 0;
        if(!$user) {
            $ip = $request->getClientIp();
            $rec = VisitDealer::where([
                'ip'=>$ip,
                'dealer_id'=>$dealerId,
            ])->orderBy('id','desc')->first();
        }
        else{
            $designerId = $user->id;
            $rec = VisitDealer::where([
                'designer_id'=>$designerId,
                'dealer_id'=>$dealerId,
            ])->orderBy('id','desc')->first();
        }
        if($rec){
            return false;
        }
        $rec = new VisitDealer();
        $rec->designer_id = $designerId;
        $rec->ip = $ip;
        $rec->dealer_id = $dealerId;
        $rec->save();
        $count = VisitDealer::where([
            'dealer_id'=>$dealerId,
        ])->count();
        DetailDealer::where('dealer_id',$dealerId)->update(['count_view'=>$count]);
        return true;
    }

    public static function favAlbum($albumId){
        $loginDesigner = Auth::user();
        $loginGuest = Auth::guard('m_guest')->user();
        if(!$loginDesigner && !$loginGuest) {
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }
        //操作标记（默认是收藏）
        $operation = 1;
        if($loginDesigner){
            $loginDesignerId = $loginDesigner->id;

            $album = Album::find($albumId);
            if($album->designer_id == $loginDesignerId){
                return [
                    'result'=>-1,
                    'msg'=>'不能关注自己的方案~',
                ];
            }
            $rec = FavAlbum::where([
                'designer_id'=>$loginDesignerId,
                'album_id'=>$albumId,
            ])->count();
            if($rec){
                FavAlbum::where([
                    'designer_id'=>$loginDesignerId,
                    'album_id'=>$albumId,
                ])->delete();
                $operation = 0;
            }
            else{
                $rec = new FavAlbum();
                $rec->designer_id = $loginDesignerId;
                $rec->album_id = $albumId;
                $rec->save();
            }
        }else if($loginGuest){
            $loginGuestId = $loginGuest->id;
            $rec = GuestFavAlbum::where([
                'guest_id'=>$loginGuestId,
                'album_id'=>$albumId,
            ])->count();
            if($rec){
                GuestFavAlbum::where([
                    'guest_id'=>$loginGuestId,
                    'album_id'=>$albumId,
                ])->delete();
                $operation = 0;
            }
            else{
                $rec = new GuestFavAlbum();
                $rec->guest_id = $loginGuestId;
                $rec->album_id = $albumId;
                $rec->save();
            }
        }else{
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }

        $count = 0;
        $designerFav = FavAlbum::where([
            'album_id'=>$albumId,
        ])->count();
        $guestFav = GuestFavAlbum::where([
            'album_id'=>$albumId,
        ])->count();
        $count = $designerFav + $guestFav;
        Album::where('id',$albumId)->update(['count_fav'=>$count]);
        return [
            'result'=>$operation?1:0,
            'msg'=>$operation?'收藏成功':'取消收藏成功',
            'count'=>$count,
        ];


    }

    public static function favDesigner($target_designer_id){
        $loginDesigner = Auth::user();
        $loginGuest = Auth::guard('m_guest')->user();
        if(!$loginDesigner && !$loginGuest) {
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }
        //操作标记（默认是关注）
        $operation = 1;
        if($loginDesigner){
            //登录者是设计师
            $loginDesignerId = $loginDesigner->id;
            if($loginDesignerId == $target_designer_id){
                return [
                    'result'=>-1,
                    'msg'=>'不能关注自己',
                ];
            }
            $rec = FavDesigner::where([
                'designer_id'=>$loginDesignerId,
                'target_designer_id'=>$target_designer_id,
            ])->count();
            if($rec){
                FavDesigner::where([
                    'designer_id'=>$loginDesignerId,
                    'target_designer_id'=>$target_designer_id,
                ])->delete();
                $operation = 0;
            }
            else{
                $rec = new FavDesigner();
                $rec->designer_id = $loginDesignerId;
                $rec->target_designer_id = $target_designer_id;
                $rec->save();
            }
        }else if($loginGuest){
            //登录者是游客
            $loginGuestId = $loginGuest->id;
            $rec = GuestFavDesigner::where([
                'guest_id'=>$loginGuestId,
                'target_designer_id'=>$target_designer_id,
            ])->count();
            if($rec){
                GuestFavDesigner::where([
                    'guest_id'=>$loginGuestId,
                    'target_designer_id'=>$target_designer_id,
                ])->delete();
                $operation = 0;
            }
            else{
                $rec = new GuestFavDesigner();
                $rec->guest_id = $loginGuestId;
                $rec->target_designer_id = $target_designer_id;
                $rec->save();
            }
        }else{
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }

        $count = 0;
        $designerFav = FavDesigner::where([
            'target_designer_id'=>$target_designer_id,
        ])->count();
        $guestFav = GuestFavDesigner::where([
            'target_designer_id'=>$target_designer_id,
        ])->count();
        $count = $designerFav + $guestFav;
        DesignerDetail::where('designer_id',$target_designer_id)->update(['count_fav'=>$count]);
        return [
            'result'=>$operation?1:0,
            'msg'=>$operation?'关注成功':'取消关注成功',
            'count'=>$count,
        ];
    }

    public static function favProduct($productId){
        $loginDesigner = Auth::user();
        $loginGuest = Auth::guard('m_guest')->user();
        if(!$loginDesigner && !$loginGuest) {
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }
        //操作标记（默认是关注）
        $operation = 1;
        if($loginDesigner){
            $loginDesignerId = $loginDesigner->id;
            $rec = FavProduct::where([
                'designer_id'=>$loginDesignerId,
                'product_id'=>$productId,
            ])->count();
            if($rec){
                FavProduct::where([
                    'designer_id'=>$loginDesignerId,
                    'product_id'=>$productId,
                ])->delete();
                $operation = 0;
            }
            else{
                $rec = new FavProduct();
                $rec->designer_id = $loginDesignerId;
                $rec->product_id = $productId;
                $rec->save();

            }
        }else if($loginGuest){
            $loginGuestId = $loginGuest->id;
            $rec = GuestFavProduct::where([
                'guest_id'=>$loginGuestId,
                'product_id'=>$productId,
            ])->count();
            if($rec){
                GuestFavProduct::where([
                    'guest_id'=>$loginGuestId,
                    'product_id'=>$productId,
                ])->delete();
                $operation = 0;
            }
            else{
                $rec = new GuestFavProduct();
                $rec->guest_id = $loginGuestId;
                $rec->product_id = $productId;
                $rec->save();

            }
        }else{
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }

        $count = 0;
        $designerFav = FavProduct::where([
            'product_id'=>$productId,
        ])->count();
        $guestFav = GuestFavProduct::where([
            'product_id'=>$productId,
        ])->count();
        $count = $designerFav + $guestFav;
        ProductCeramic::where('id',$productId)->update(['count_fav'=>$count]);
        return [
            'result'=>$operation?1:0,
            'msg'=>$operation?'收藏成功':'取消收藏成功',
            'count'=>$count,
        ];

    }

    public static function favDealer($targetDealerId){
        $loginDesigner = Auth::user();
        $loginGuest = Auth::guard('m_guest')->user();
        if(!$loginDesigner && !$loginGuest) {
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }
        //操作标记（默认是收藏）
        $operation = 1;
        if($loginDesigner){
            //登录的是设计师
            $loginDesignerId = $loginDesigner->id;
            $rec = FavDealer::where([
                'designer_id'=>$loginDesignerId,
                'target_dealer_id'=>$targetDealerId,
            ])->count();
            if($rec){
                FavDealer::where([
                    'designer_id'=>$loginDesignerId,
                    'target_dealer_id'=>$targetDealerId,
                ])->delete();
                $operation = 0;
            }
            else{
                $rec = new FavDealer();
                $rec->designer_id = $loginDesignerId;
                $rec->target_dealer_id = $targetDealerId;
                $rec->save();
            }
        }else if($loginGuest){
            $loginGuestId = $loginGuest->id;
            $rec = GuestFavDealer::where([
                'guest_id'=>$loginGuestId,
                'target_dealer_id'=>$targetDealerId,
            ])->count();
            if($rec){
                GuestFavDealer::where([
                    'guest_id'=>$loginGuestId,
                    'target_dealer_id'=>$targetDealerId,
                ])->delete();
                $operation = 0;
            }
            else{
                $rec = new GuestFavDealer();
                $rec->guest_id = $loginGuestId;
                $rec->target_dealer_id = $targetDealerId;
                $rec->save();
            }
        }else{
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }

        $count = 0;
        $guestFavCount = GuestFavDealer::where([
            'target_dealer_id'=>$targetDealerId,
        ])->count();
        $designerFavCount = FavDealer::where([
            'target_dealer_id'=>$targetDealerId,
        ])->count();
        $count = $guestFavCount + $designerFavCount;

        DetailDealer::where('dealer_id',$targetDealerId)->update(['count_fav'=>$count]);
        return [
            'result'=>$operation?1:0,
            'msg'=>$operation?'收藏成功':'取消收藏成功',
            'count'=>$count,
        ];

    }

    public static function likeAlbum($albumId){
        $loginDesigner = Auth::user();
        $loginGuest = Auth::guard('m_guest')->user();
        if(!$loginDesigner && !$loginGuest) {
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }
        //操作标记（默认是关注）
        $operation = 1;
        if($loginDesigner){
            $loginDesignerId = $loginDesigner->id;
            $album = Album::find($albumId);
            if($album->designer_id == $loginDesignerId){
                return [
                    'result'=>-1,
                    'msg'=>'不能点赞自己的方案~',
                ];
            }
            $rec = LikeAlbum::where([
                'designer_id'=>$loginDesignerId,
                'album_id'=>$albumId,
            ])->count();
            if($rec){
                LikeAlbum::where([
                    'designer_id'=>$loginDesignerId,
                    'album_id'=>$albumId,
                ])->delete();
                $operation = 0;
            }
            else{
                $rec = new LikeAlbum();
                $rec->designer_id = $loginDesignerId;
                $rec->album_id = $albumId;
                $rec->save();
            }
        }else if($loginGuest){
            $loginGuestId = $loginGuest->id;
            $rec = GuestLikeAlbum::where([
                'guest_id'=>$loginGuestId,
                'album_id'=>$albumId,
            ])->count();
            if($rec){
                GuestLikeAlbum::where([
                    'guest_id'=>$loginGuestId,
                    'album_id'=>$albumId,
                ])->delete();
                $operation = 0;
            }
            else{
                $rec = new GuestLikeAlbum();
                $rec->guest_id = $loginGuestId;
                $rec->album_id = $albumId;
                $rec->save();
            }
        }else{
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }

        $count = 0;
        $designerLike = LikeAlbum::where([
            'album_id'=>$albumId,
        ])->count();
        $guestLike = GuestLikeAlbum::where([
            'album_id'=>$albumId,
        ])->count();
        $count = $designerLike + $guestLike;
        Album::where('id',$albumId)->update(['count_praise'=>$count]);
        return [
            'result'=>$operation?1:0,
            'msg'=>$operation?'点赞成功':'取消点赞成功',
            'count'=>$count,
        ];

    }

    public function clearVisit(){
        $stopTime = Carbon::today()->subDays(30);
        VisitAlbum::where('created_at','<',$stopTime)->delete();
        VisitDesigner::where('created_at','<',$stopTime)->delete();
        VisitProduct::where('created_at','<',$stopTime)->delete();
        VisitDealer::where('created_at','<',$stopTime)->delete();
    }

}