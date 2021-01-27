<?php

namespace App\Services\v1\site;

use App\Http\Services\common\SystemLogService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\OrganizationDealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Http\Services\common\GetVerifiCodeService;

class LocationService
{
    public static function getWxLocationCity(Request $request)
    {
        $ip = $request->input('ip','');
        $loginDesigner = Auth::user();
        if(!$loginDesigner||!$loginDesigner->id){
            $cityId = session()->get('location_city');
            $city = \DB::table('areas')->where('level',2)->where('id',$cityId)->first();
            $province= \DB::table('areas')->where('level',1)->where('id',$city->pid)->first();
            return [
                'ip'=>$ip,
                'city_id'=>$cityId,
                'city'=>$city->shortname,
                'province_id'=>$province->id,
                'province'=>$province->shortname,
            ];
        }
        if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
            return [
                'ip'=>$ip,
                'data'=>[],
                'response'=>'',
                'city'=>$ip,
                'city_id'=>0,
                'province_id'=>0
            ];
        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            $dealer = OrganizationDealer::find($loginDesigner->organization_id);
            while($dealer->p_dealer_id>0){
                $dealer = OrganizationDealer::find($dealer->p_dealer_id);
            }
            $dealer = DetailDealer::where('dealer_id',$dealer->id)->first();
            $cities = $dealer->area_visible_city;
            $cities = explode('|',$cities);
            $cityId = $cities[1];
            $request->session()->put('location_code', $cityId);
            $city = \DB::table('areas')->where('level',2)->where('id',$cityId)->first();
            $province= \DB::table('areas')->where('level',1)->where('id',$city->pid)->first();
            return [
                'ip'=>$ip,
                'city_id'=>$cityId,
                'city'=>$city->shortname,
                'province_id'=>$province->id,
                'province'=>$province->shortname,
            ];
        }
        else{}
    }

    public static function getClientCity(Request $request)
    {
        $ip = $request->input('ip','');
        if($ip=='')
            $ip = $request->getClientIp();
        //必须先登录
        $loginDesigner = Auth::user();
        if(!$loginDesigner||!$loginDesigner->id){
            return [
                'ip'=>$ip,
                'data'=>[],
                'response'=>'',
                'city'=>$ip,
                'city_id'=>0,
                'province_id'=>0
            ];
        }
        if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
            return [
                'ip'=>$ip,
                'data'=>[],
                'response'=>'',
                'city'=>$ip,
                'city_id'=>0,
                'province_id'=>0
            ];
        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            $dealer = OrganizationDealer::find($loginDesigner->organization_id);
            while($dealer->p_dealer_id>0){
                $dealer = OrganizationDealer::find($dealer->p_dealer_id);
            }
            $dealer = DetailDealer::where('dealer_id',$dealer->id)->first();
            $cities = $dealer->area_visible_city;
            $cities = explode('|',$cities);
            $cityId = $cities[1];
            $request->session()->put('location_code', $cityId);
            $city = \DB::table('areas')->where('level',2)->where('id',$cityId)->first();
            $province= \DB::table('areas')->where('level',1)->where('id',$city->pid)->first();
            return [
                'ip'=>$ip,
                'city_id'=>$cityId,
                'city'=>$city->shortname,
                'province_id'=>$province->id,
                'province'=>$province->shortname,
            ];
        }
        else{}
        /*//$cityId = $request->session()->get('location_code');
        $cityId = null;
        $city = null;
        if ($cityId) {
            $city = \DB::table('areas')->where('id',$cityId)->where('level',2)->first();
        }
        if(!$city) {
            $ip = $request->input('ip','');
            if($ip=='')
                $ip = $request->getClientIp();
            $URL='http://api.pi.do/api/v1/queryip?ip='.$ip;
            $json = GetVerifiCodeService::curl($URL);
            $response = json_decode($json);

            $city=0;
            $province=0;
            if($response&&$response->statuscode==0){
                $province = $response->data->ipInfo->Province;
                $province = mb_substr($province,0,mb_strlen($province,'utf-8')-1,'utf-8');
                $city = $response->data->ipInfo->City;
                $city = \DB::table('areas')
                    ->where('level',2)
                    ->where('name',$city)
                    ->where('merger_name','like','%'.$province.'%')
                    ->first();
            }
            if(!$city){
                return [
                    'ip'=>$ip,
                    'data'=>isset($response->data)?$response->data:[],
                    'response'=>$response,
                    'city'=>$ip,
                    'city_id'=>0,
                    'province_id'=>0
                ];
                //$city = \DB::table('areas')->where('level',2)->where('id',2011)->first();
            }
        }

        if($city){
            $cityId = $city->id;
            $request->session()->put('location_code', $cityId);
            $province= \DB::table('areas')->where('level',1)->where('id',$city->pid)->first();
            return [
                'ip'=>$ip,
                'city_id'=>$cityId,
                'city'=>$city->shortname,
                'province_id'=>$province->id,
                'province'=>$province->shortname,
            ];
        }*/
    }

    //根据服务区和服务区域权限，计算服务范围
    //特殊情况下，可使用$stopLevel限制计算范围的最高级别（0:市/1:省/2:国）
    public static function getServingArea($district, $privilege=0, $stopLevel=2){
        $districtString = '';
        $cityString = '';
        $provinceString = '';
        $countryString = '中国';
        $districtStringShort = '';
        $cityStringShort = '';
        $provinceStringShort = '';
        $districtId = 0;
        $cityId = 0;
        $provinceId = 0;
        $countryId = 0;

        $city = Area::find($district);
        $district = $city;
        if($city&&$city->level==3){
            $districtId = $city->id;
            $districtString = $city->name;
            $districtStringShort = $city->shortname;
            $city = Area::find($city->pid);
            if($city&&$city->level==2){
                $cityId = $city->id;
                $cityString = $city->name;
                $cityStringShort = $city->shortname;
                $province = Area::find($city->pid);
                if($province&&$province->level==1) {
                    $provinceId = $province->id;
                    $provinceString = $province->name;
                    $provinceStringShort = $province->shortname;
                }
            }
        }
        if($privilege<$stopLevel){
            $privilege = $stopLevel;
        }
        $data = [
            'districtId'=>$districtId,
            'cityId'=>$cityId,
            'provinceId'=>$provinceId,
            'countryId'=>$countryId,
            'districtString'=>$districtString,
            'districtStringShort'=>$districtString,
            'cityString'=>$cityString,
            'cityStringShort'=>$cityString,
            'provinceString'=>$provinceString,
            'provinceStringShort'=>$provinceString,
            'countryString'=>$countryString,
            'privilege'=>$privilege,
            'stopLevel'=>$stopLevel,
            'lng'=>$district->lng,
            'lat'=>$district->lat,
        ];
        switch($privilege){
            case DetailDealer::PRIVILEGE_AREA_SERVING_COUNTRY://2
                //$data['mergeString'] = $countryString;
                $data['mergeString'] = $provinceString.'，'.$cityString.'，'.$districtString;
                $data['mergeStringLight'] = $provinceString.$cityString.$districtString;
                $data['mergeStringShortLight'] = $provinceStringShort.$cityStringShort.$districtStringShort;
                break;
            case DetailDealer::PRIVILEGE_AREA_SERVING_PROVINCE://1
                $data['mergeString'] = $provinceString;
                $data['mergeStringLight'] = $provinceString;
                $data['mergeStringShortLight'] = $provinceStringShort;
                break;
            default:
                $data['mergeString'] = $provinceString.'，'.$cityString;
                $data['mergeStringLight'] = $provinceString.$cityString;
                $data['mergeStringShortLight'] = $provinceStringShort.$cityStringShort;
                break;
        }
        return $data;
    }

}