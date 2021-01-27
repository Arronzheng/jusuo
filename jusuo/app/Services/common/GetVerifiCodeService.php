<?php
namespace App\Http\Services\common;

class GetVerifiCodeService
{
    public function sendMobile($send_no, $msg)
    {
        $CorpID = 300918;
        $strPasswd = 'Mh902115';
        $LoginName = 'JDSJY';

        //date_default_timezone_set('Asia/Shanghai');//注意设置
        $msg = iconv('UTF-8', 'gb2312', $msg);
        $strTimeStamp = date('m') . date('d') . date('H') . date('i') . date('s');
        $strInput = $CorpID . $strPasswd . $strTimeStamp;
        $strMd5 = md5($strInput);
        $URL = "http://sms3.mobset.com/SDK2/Sms_Send.asp?CorpID=" . $CorpID .
            "&LoginName=" . $LoginName .
            "&TimeStamp=" . $strTimeStamp .
            "&Passwd=" . $strMd5 .
            "&send_no=" . $send_no .
            "&msg=" . $msg .
            "&LongSms=1";

        $data = $this->curl($URL);

        if (!$data) {
            return FALSE;
        }

        list($status) = explode(',', $data);

        return $status == 1 ? true : false;
    }

    public static function curl($url, $post = 0, $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);//设置超时
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            return FALSE;
        }

        curl_close($ch);

        return $data;
    }

}