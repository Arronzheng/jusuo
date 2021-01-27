<?php

namespace App\Services\v1\common;

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
use App\Models\MobileCaptcha;
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
use App\Services\v1\site\ApiService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CaptchaService
{
    private $resend_time = 60;   //前端重复获取验证码间隔时间
    private $expired_time = 600; //验证码过期时间10分钟

    function __construct()
    {
    }

    public function check_mobile_capture($mobile,$code,$type=MobileCaptcha::TYPE_REGISTER)
    {
        $mobile_captcha = MobileCaptcha::where('mobile',$mobile)
            ->where('type',$type)->first();

        if(!$mobile_captcha){
            return [
                'status' => 0,
                'msg'  =>  '验证码已失效'
            ];
        }

        //10分钟过期，600秒
        if( (time()-strtotime($mobile_captcha->updated_at)) > $this->expired_time ){
            return [
                'status' => 0,
                'msg'  =>  '验证码已过期'
            ];
        }

        $captcha_code = $mobile_captcha->captcha;

        if(!$captcha_code) {
            return [
                'status' => 0,
                'msg'  =>  '验证码已失效'
            ];
        }

        if($code != $captcha_code) {
            return [
                'status' => 0,
                'msg'  =>  '验证码不正确'
            ];
        }

        return [
            'status' => 1,
            'msg'  =>  '验证码正确'
        ];
    }

    public function send_captcha($mobile,$type=MobileCaptcha::TYPE_REGISTER)
    {
        $result = array();
        $result['status'] = 1;
        $result['msg'] = '';

        //验证码字符串
        //$code = str_pad(random_int(1,9999),4,0,STR_PAD_LEFT);
        $code = '1234';

        try{

            DB::beginTransaction();

            $mobileCaptcha = MobileCaptcha::where('type',$type)
                ->where('mobile',$mobile)->first();

            if($mobileCaptcha){
                if( time() - (strtotime($mobileCaptcha->updated_at)) < $this->resend_time) {
                    $result['status'] = 0;
                    $result['msg'] = $this->resend_time+'秒内只能发送一次';
                }else{
                    $mobileCaptcha->captcha = $code;
                    $mobileCaptcha->updated_at = Carbon::now();
                    $mobileCaptcha->save();
                }
            }else{
                $check = new MobileCaptcha();
                $check->type = $type;
                $check->mobile = $mobile;
                $check->captcha = $code;
                $check->save();
            }

            //发送手机验证码
            /*$result = $easySms->send($mobile, [
                'content' => "验证码为{$code}"
            ]);*/

            DB::commit();

            $result['msg'] = '验证码发送成功';
            return $result;

        } catch (\Exception $exception){
            DB::rollback();

            $result['msg'] = '短信平台繁忙，请稍后再试';
            return $result;

        }

    }

}