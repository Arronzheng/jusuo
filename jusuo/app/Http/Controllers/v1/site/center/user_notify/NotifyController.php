<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2020/2/21
 * Time: 15:17
 */
namespace App\Http\Controllers\v1\site\center\user_notify;

use App\Http\Services\v1\admin\AuthService;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class NotifyController extends ApiController{

    private $authService;

    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    public function commentList(Request $request){
        $designer = $request->user();
        $album_ids = Album::where('designer_id',$designer->id)->pluck('id')->toArray();
        $comments = AlbumComments::whereIn('album_id',$album_ids)->orderBy('created_at','desc')->get();

        $comments->transform(function($v) use ($designer){
            $send_designer = DesignerDetail::where('designer_id',$v->designer_id)->first();
            if($send_designer){
                $v->sender_name = $send_designer->nickname;
                if(!Str::startsWith($send_designer->url_avatar,['http://','https://'])){
                    $send_designer->url_avatar = url($send_designer->url_avatar);
                }
                $v->sender_avatar = $send_designer->url_avatar;
            }

            $album = Album::where('id',$v->album_id)->first();
            if($album){
                $v->album_title = $album->title;
            }

            $v->time = $this->timeTransform($v->created_at);

            return $v;
        });

        return $this->respDataReturn($comments);
    }

    public function favList(Request $request){
        $designer = $request->user();


        //关注
        $designer_fav = FavDesigner::where('target_designer_id',$designer->id)->get();

        $designer_fav->transform(function($v) use ($designer){
            $sender_user = Designer::where('id',$v->designer_id)->first();
            $sender = DesignerDetail::where('designer_id',$v->designer_id)->first();

            if($sender){
                $v->fav = false;
                if($designer){
                    $focused = FavDesigner::where('target_designer_id',$v->designer_id)->where('designer_id',$designer->id)->first();
                    if($focused){ $v->fav = true; }
                }


                $v->sender_name = $sender->nickname;
                if(!Str::startsWith($sender->url_avatar,['http://','https://'])){
                    $sender->url_avatar = url($sender->url_avatar);
                }
                $v->sender_avatar = $sender->url_avatar;
                $v->sender_web_id_code = $sender_user->web_id_code;
            }
            $v->notify_type = 0;

            $v->time = $this->timeTransform($v->created_at);

            return $v;
        });
        $designer_fav = $designer_fav->toArray();



        //点赞
        $album_ids = $designer->albums()->pluck('id')->toArray();

        $like = FavAlbum::whereIn('album_id',$album_ids)->get();
        $like->transform(function($v) use ($designer){
            $sender = DesignerDetail::where('designer_id',$v->designer_id)->first();
            $sender_user = Designer::where('id',$v->designer_id)->first();

            if($sender){

                $v->fav = false;
                if($designer){
                    $focused = FavDesigner::where('target_designer_id',$v->designer_id)->where('designer_id',$designer->id)->first();
                    if($focused){ $v->fav = true; }
                }

                $v->sender_name = $sender->nickname;
                if(!Str::startsWith($sender->url_avatar,['http://','https://'])){
                    $sender->url_avatar = url($sender->url_avatar);
                }
                $v->sender_avatar = $sender->url_avatar;
                $v->sender_web_id_code = $sender_user->web_id_code;
            }
            $album = Album::where('id',$v->album_id)->first();
            if($album){
                $v->album_title = $album->title;
            }
            $v->notify_type = 1;

            $v->time = $this->timeTransform($v->created_at);

            return $v;
        });
        $like = $like->toArray();

        //收藏
        $collect = FavAlbum::whereIn('album_id',$album_ids)->get();
        $collect->transform(function ($v)use ($designer){
            $sender = DesignerDetail::where('designer_id',$v->designer_id)->first();
            $sender_user = Designer::where('id',$v->designer_id)->first();

            if($sender){

                $v->fav = false;
                if($designer){
                    $focused = FavDesigner::where('target_designer_id',$v->designer_id)->where('designer_id',$designer->id)->first();
                    if($focused){ $v->fav = true; }
                }

                $v->sender_name = $sender->nickname;
                if(!Str::startsWith($sender->url_avatar,['http://','https://'])){
                    $sender->url_avatar = url($sender->url_avatar);
                }
                $v->sender_avatar = $sender->url_avatar;
                $v->sender_web_id_code = $sender_user->web_id_code;
            }
            
            $album = Album::where('id',$v->album_id)->first();
            if($album){
                $v->album_title = $album->title;
            }
            $v->notify_type = 2;

            $v->time = $this->timeTransform($v->created_at);

            return $v;
        });
        $collect = $collect->toArray();

        $data = array_merge($designer_fav,$like,$collect);

        $dataCollection = collect($data)->sortByDesc('created_at')->values();


        return $this->respDataReturn($dataCollection);
    }

    public function sysNotify(Request $request){
        $designer = $request->user();


        //系统通知
        $sys = DB::table('msg_system_designers')->where('designer_id',$designer->id)->get();
        $sys->transform(function($v){
            $v->type_text = '系统通知';
            return $v;
        });
        $sys = $sys->toArray();

        //方案通知
        $album = DB::table('msg_album_designers')->where('designer_id',$designer->id)->get();
        $album->transform(function($v){
            $v->type_text = '方案通知';
            return $v;
        });
        $album = $album->toArray();


        //账号通知
        $account = DB::table('msg_account_designers')->where('designer_id',$designer->id)->get();
        $account->transform(function($v){
            $v->type_text = '账号通知';
            return $v;
        });
        $account = $account->toArray();

        $data = array_merge($sys,$album,$account);

        $dataCollection = collect($data)->sortByDesc('created_at')->values();
        $dataCollection->transform(function($v){
            $v->time = $this->timeTransform($v->created_at);
            return $v;
        });

        return $this->respDataReturn($dataCollection);

    }

    public function timeTransform($time_string){
        $time = Carbon::createFromFormat("Y-m-d H:i:s",$time_string);
        $now = Carbon::now();
        $diff_year = $now->diffInYears($time);
        if($diff_year > 0){
            $year = $diff_year.'年前';
            return $year;
        }

        $diff_month = $now->diffInMonths($time);
        if($diff_month > 0){
            $month = $diff_month.'个月前';
            return $month;
        }

        $diff_day = $now->diffInDays($time);
        if($diff_day > 0){
            $day = $diff_day.'天前';
            return $day;
        }

        $diff_hour = $now->diffInHours($time);
        if($diff_hour > 0 && $diff_hour < 24){
            $hour = $diff_hour.'小时前';
            return $hour;
        }

        $diff_min = $now->diffInMinutes($time);
        if($diff_min > 0){
            $min = $diff_min.'分钟前';
            return $min;
        }

        $diff_s = $now->diffInSeconds($time);
        if($diff_s > 0){
            return '刚刚';
        }
    }
}