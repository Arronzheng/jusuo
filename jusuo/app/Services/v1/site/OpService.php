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
use Carbon\Carbon;
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
        $user = Auth::user();
        if(!$user) {
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }
        $designerId = $user->id;
        $rec = FavAlbum::where([
            'designer_id'=>$designerId,
            'album_id'=>$albumId,
        ])->count();
        if($rec){
            FavAlbum::where([
                'designer_id'=>$designerId,
                'album_id'=>$albumId,
            ])->delete();
            $count = FavAlbum::where([
                'album_id'=>$albumId,
            ])->count();
            Album::where('id',$albumId)->update(['count_fav'=>$count]);
            return [
                'result'=>0,
                'msg'=>'取消收藏成功',
                'count'=>$count,
            ];
        }
        else{
            $rec = new FavAlbum();
            $rec->designer_id = $designerId;
            $rec->album_id = $albumId;
            $rec->save();
            $count = FavAlbum::where([
                'album_id'=>$albumId,
            ])->count();
            Album::where('id',$albumId)->update(['count_fav'=>$count]);
            return [
                'result'=>1,
                'msg'=>'收藏成功',
                'count'=>$count,
            ];
        }
    }

    public static function favDesigner($target_designer_id){
        $user = Auth::user();
        if(!$user) {
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }
        $designerId = $user->id;
        $rec = FavDesigner::where([
            'designer_id'=>$designerId,
            'target_designer_id'=>$target_designer_id,
        ])->count();
        if($rec){
            FavDesigner::where([
                'designer_id'=>$designerId,
                'target_designer_id'=>$target_designer_id,
            ])->delete();
            $count = FavDesigner::where([
                'designer_id'=>$designerId,
            ])->count();
            $count_fan = FavDesigner::where([
                'target_designer_id'=>$target_designer_id,
            ])->count();
            DesignerDetail::where('designer_id',$designerId)->update(['count_fav'=>$count]);
            DesignerDetail::where('designer_id',$target_designer_id)->update(['count_fan'=>$count_fan]);
            return [
                'result'=>0,
                'msg'=>'取消关注成功',
                'count'=>$count_fan,
            ];
        }
        else{
            $rec = new FavDesigner();
            $rec->designer_id = $designerId;
            $rec->target_designer_id = $target_designer_id;
            $rec->save();
            $count = FavDesigner::where([
                'designer_id'=>$designerId,
            ])->count();
            $count_fan = FavDesigner::where([
                'target_designer_id'=>$target_designer_id,
            ])->count();
            DesignerDetail::where('designer_id',$designerId)->update(['count_fav'=>$count]);
            DesignerDetail::where('designer_id',$target_designer_id)->update(['count_fan'=>$count_fan]);
            return [
                'result'=>1,
                'msg'=>'关注成功',
                'count'=>$count_fan,
            ];
        }
    }

    public static function favProduct($productId){
        $user = Auth::user();
        if(!$user) {
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }
        $designerId = $user->id;
        $rec = FavProduct::where([
            'designer_id'=>$designerId,
            'product_id'=>$productId,
        ])->count();
        if($rec){
            FavProduct::where([
                'designer_id'=>$designerId,
                'product_id'=>$productId,
            ])->delete();
            $count = FavProduct::where([
                'product_id'=>$productId,
            ])->count();
            ProductCeramic::where('id',$productId)->update(['count_fav'=>$count]);
            return [
                'result'=>0,
                'msg'=>'取消收藏成功',
                'count'=>$count,
            ];
        }
        else{
            $rec = new FavProduct();
            $rec->designer_id = $designerId;
            $rec->product_id = $productId;
            $rec->save();
            $count = FavProduct::where([
                'product_id'=>$productId,
            ])->count();
            ProductCeramic::where('id',$productId)->update(['count_fav'=>$count]);
            return [
                'result'=>1,
                'msg'=>'收藏成功',
                'count'=>$count,
            ];
        }
    }

    public static function favDealer($targetDealerId){
        $user = Auth::user();
        if(!$user) {
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }
        $designerId = $user->id;
        $rec = FavDealer::where([
            'designer_id'=>$designerId,
            'target_dealer_id'=>$targetDealerId,
        ])->count();
        if($rec){
            FavDealer::where([
                'designer_id'=>$designerId,
                'target_dealer_id'=>$targetDealerId,
            ])->delete();
            $count = FavDealer::where([
                'target_dealer_id'=>$targetDealerId,
            ])->count();
            DetailDealer::where('id',$targetDealerId)->update(['count_fav'=>$count]);
            return [
                'result'=>0,
                'msg'=>'取消收藏成功',
                'count'=>$count,
            ];
        }
        else{
            $rec = new FavDealer();
            $rec->designer_id = $designerId;
            $rec->target_dealer_id = $targetDealerId;
            $rec->save();
            $count = FavDealer::where([
                'target_dealer_id'=>$targetDealerId,
            ])->count();
            DetailDealer::where('id',$targetDealerId)->update(['count_fav'=>$count]);
            return [
                'result'=>1,
                'msg'=>'收藏成功',
                'count'=>$count,
            ];
        }
    }

    public static function likeAlbum($albumId){
        $user = Auth::user();
        if(!$user) {
            return [
                'result'=>-1,
                'msg'=>'未登录',
            ];
        }
        $designerId = $user->id;
        $rec = LikeAlbum::where([
            'designer_id'=>$designerId,
            'album_id'=>$albumId,
        ])->count();
        if($rec){
            LikeAlbum::where([
                'designer_id'=>$designerId,
                'album_id'=>$albumId,
            ])->delete();
            $count = LikeAlbum::where([
                'album_id'=>$albumId,
            ])->count();
            Album::where('id',$albumId)->update(['count_praise'=>$count]);
            $album = Album::find($albumId);
            $target_designer_id = $album->designer_id;
            $countPraise = Album::where([
                'designer_id'=>$target_designer_id,
                'status'=>Album::STATUS_PASS,
                'visible_status'=>Album::VISIBLE_STATUS_ON,
            ])->sum('count_praise');
            DesignerDetail::where('designer_id',$target_designer_id)->update(['count_praise'=>$countPraise]);
            return [
                'result'=>0,
                'msg'=>'取消点赞成功',
                'count'=>$count,
            ];
        }
        else{
            $rec = new LikeAlbum();
            $rec->designer_id = $designerId;
            $rec->album_id = $albumId;
            $rec->save();
            $count = LikeAlbum::where([
                'album_id'=>$albumId,
            ])->count();
            Album::where('id',$albumId)->update(['count_praise'=>$count]);
            $album = Album::find($albumId);
            $target_designer_id = $album->designer_id;
            $countPraise = Album::where([
                'designer_id'=>$target_designer_id,
                'status'=>Album::STATUS_PASS,
                'visible_status'=>Album::VISIBLE_STATUS_ON,
            ])->sum('count_praise');
            DesignerDetail::where('designer_id',$target_designer_id)->update(['count_praise'=>$countPraise]);
            return [
                'result'=>1,
                'msg'=>'点赞成功',
                'count'=>$count,
            ];
        }
    }

    public function clearVisit(){
        $stopTime = Carbon::today()->subDays(30);
        VisitAlbum::where('created_at','<',$stopTime)->delete();
        VisitDesigner::where('created_at','<',$stopTime)->delete();
        VisitProduct::where('created_at','<',$stopTime)->delete();
        VisitDealer::where('created_at','<',$stopTime)->delete();
    }

}