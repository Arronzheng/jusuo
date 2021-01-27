<?php

namespace App\Services\v1\site;


use App\Exceptions\ArticleException\ArticleNotFound;
use App\Exceptions\ArticleException\ChannelNotFound;
use App\Exceptions\SupplyException\SupplyNotFound;
use App\Models\Designer;
use App\Models\MobileCaptcha;
use App\Models\WynArea;
use App\Models\WynArticle;
use App\Models\WynArticleComment;
use App\Models\WynChannel;
use App\Models\WynChannelSubscribe;
use App\Models\WynMember;
use App\Models\WynMobileCaptcha;
use App\Models\WynSdCategory;
use App\Models\WynSiteConfig;
use App\Models\WynSupply;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LoginService
{

	public static function check_mobile_capture($mobile,$code,$type=MobileCaptcha::TYPE_REGISTER)
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
		if( (time()-strtotime($mobile_captcha->updated_at)) > 600 ){
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

	public static function random_token(){
		$uid = sha1(uniqid(microtime(true), true));
		$data = $_SERVER['REQUEST_TIME'];
		$data .= isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
		$data .= $_SERVER['REMOTE_ADDR'];
		$data .= $_SERVER['REMOTE_PORT'];
		$data = sha1($data);
		$hash = hash('sha256', $uid . md5($data));

		while(Designer::where('remember_token',$hash)->count()>0){
			$uid = sha1(uniqid(microtime(true), true));
			$hash = hash('sha256', $uid . $data);
		}


		return $hash;
	}



}