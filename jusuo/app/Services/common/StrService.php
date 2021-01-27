<?php
namespace App\Http\Services\common;

use App\Models\MemberDetail;
use App\Models\OrganizationDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StrService{

    const numChar = ['零','一','二','三','四','五','六','七','八','九','十'];

    public static function strRandom($length = 16)
    {
        return Str::random($length);
    }

    public function str_designer_id($length = 8)
    {
        $str = substr(md5(time()), 0, $length);
        while(MemberDetail::where('code_designer_id',$str)->first()){
            $str = substr(md5(time()), 0, $length);
        }
        return $str;
    }

    public static function str_organization_id_code()
    {
        $str = self::str_random(8);
        while (OrganizationDetail::where('organization_id_code', $str)->first()){
            $str = $str = self::str_random(8);
        }
        return $str;
    }

    public static function str_num_to_char($num=0)
    {
        $numChar = StrService::numChar;
        if($num>=count($numChar)){
            return $num;
        }
        else{
            return $numChar[$num];
        }
    }

    public static function str_random_field_value($table, $uniqueField, $length = 16, $tryTime=10)
    {
        $try = $tryTime;
        do {
            $string = str_random($length);
            $collect = DB::table($table)->where($uniqueField, $string)->first();
            $try--;
        }
        while($collect&&$try>0);
        return [
            'string'=>$string,
            'table'=>$table,
            'field'=>$uniqueField,
            'tryLeft'=>$try,
        ];
    }

    //比较快速的方法，只要传入表名，就可生成web_id_code
    public static function str_table_field_unique($table, $field='web_id_code', $length=16, $tryTime=10)
    {
        $rec = DB::table($table)->where($field,'')->get(['id',$field]);
        $count = count($rec);
        foreach($rec as $v){
            $id_code = StrService::str_random_field_value($table, $field, $length, $tryTime);
            if($id_code['tryLeft']>0){
                DB::table($table)->where('id',$v->id)->update([
                    $field => $id_code['string']
                ]);
                $count--;
            }
        }
        return $count;//如果是0，代表全部成功
    }

    //适用于品牌、销售商、设计师、产品
    public static function get_id_by_web_code($table,$code){
        if($code=='')
            return 0;
        $rec = DB::table($table)->where('web_id_code',$code)->first();
        return $rec?$rec->id:-1;
    }

    public static function get_web_code_by_id($table,$id){
        if($id<=0)
            return '';
        $rec = DB::table($table)->find($id);
        return $rec?$rec->web_id_code:'';
    }

    //适用于方案
    public static function get_album_id_by_web_code($code){
        if($code=='')
            return null;
        $rec = DB::table('search_albums')->where('web_id_code',$code)->first();
        return $rec?$rec->album_id:null;
    }

    public static function get_web_code_by_album_id($id){
        if($id<=0)
            return '';
        $rec = DB::table('search_albums')->where('album_id',$id)->first();
        return $rec?$rec->web_id_code:'';
    }
}