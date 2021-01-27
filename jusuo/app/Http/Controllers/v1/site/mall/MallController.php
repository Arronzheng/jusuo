<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2019/12/16
 * Time: 14:47
 */

namespace App\Http\Controllers\v1\site\mall;


use App\Http\Controllers\v1\VersionController;
use App\Models\Area;
use App\Models\ShoppingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MallController extends VersionController{


    public function index(Request $request){

        $keyword = $request->input('k','');

        return $this->get_view('v1.site.mall.index',compact('keyword'));

    }

    public function address_create(Request $request){
        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();
        return $this->get_view('v1.site.mall.address.edit',compact('provinces'));
    }

    public function address_edit($id,Request $request){
        //省份数据
        $data = ShoppingAddress::where('designer_id',Auth::user()->id)->find($id);

        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();
        $cities = [];
        $districts = [];
        if($data->province_id){
            $cities = Area::where('level',2)->where('pid',$data->province_id)->orderBy('id','asc')->select(['id','name'])->get();
        }
        if($data->city_id){
            $districts = Area::where('level',3)->where('pid',$data->city_id)->orderBy('id','asc')->select(['id','name'])->get();
        }
        return $this->get_view('v1.site.mall.address.edit',compact('provinces','cities','districts','data'));
    }

    public function detail($web_id_code,Request $request)
    {
        return $this->get_view('v1.site.mall.detail',compact('web_id_code'));
    }

    public function confirm_order(Request $request)
    {

        return $this->get_view('v1.site.mall.sheet');
    }

}