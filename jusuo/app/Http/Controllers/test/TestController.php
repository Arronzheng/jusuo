<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{

    public function test1()
    {
        date_default_timezone_set('Asia/Shanghai');

        $data = 'a:10:{s:10:"brand_name";s:9:"新品牌";s:12:"brand_domain";s:4:"xpp1";s:12:"company_name";s:12:"测试公司";s:18:"social_credit_code";s:4:"aaaa";s:17:"legal_person_name";s:3:"bbb";s:9:"idcard_no";s:4:"cccc";s:17:"idcard_expired_at";s:10:"2019-09-27";s:16:"url_idcard_front";s:88:"/storage/images/cert/idcard/brand/09/27/Gw/28yPEmv5nA3vNAzYqeLdrx0xKVX3l3TLsVi1Mlbq.jpeg";s:15:"url_idcard_back";s:88:"/storage/images/cert/idcard/brand/09/27/Ay/2Bp8UhWwJcjPIK8r1wljZEHsVejFDFLyODkKQcwC.jpeg";s:11:"url_license";s:98:"/storage/images/cert/business_licence/brand/09/27/G0/Gcw7QxlTuiiEif0lY8YfyXNkeUXISOvfe4Z088CU.jpeg";}';

        $new_data = unserialize($data);
        $new_data['name'] = '测试公司';
        $new_data['code_idcard'] = '440682199201088888';
        $new_data['code_license'] = '65468761465464';
        $new_data['expired_at_idcard'] = '2019-01-01';

        $result = serialize($new_data);

        echo $result;
    }

    public function read_log(Request $request)
    {
        header("Content-type: text/html; charset=utf-8");
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        $date = $request->input('date',date('Y-m-d',time()));
        $file = $request->input('file','laravel-'.$date.'.log');

        $log_path = storage_path('logs/'.$file);

        if(file_exists($log_path)){
            @chmod($log_path,0777);
        }else{
            @chmod($log_path,0777);
            Log::info('start log');
        }

        $result = file_get_contents($log_path);

        echo "<pre>".$result."</pre>";
    }

    public function clear_log(Request $request)
    {
        header("Content-type: text/html; charset=gb2312");

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        $date = $request->input('date',date('Y-m-d',time()));
        $file = $request->input('file','laravel-'.$date.'.log');

        $result = @unlink (storage_path('logs/'.$file));

        echo "<pre>".json_encode($result)."</pre>";
    }

    public static function clear_session(Request $request)
    {
        $request->session()->flush();

        echo 'success';
    }


}
