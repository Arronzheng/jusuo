<?php

namespace App\Http\Controllers\v1\admin\admin\platform;

use App\Http\Controllers\v1\VersionController;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class PlatformController extends VersionController
{

    public function index(Request $request)
    {

        $requestData = $request->all();

        $entry = TestData::query();
        
        if(isset($requestData['keyword']) && $requestData['keyword']!==''){
            $entry = $entry->where('name','like','%'.$requestData['keyword'].'%');
        }

        if(isset($requestData['status']) && $requestData['status']!==''){
            $entry = $entry->where('status',$requestData['status']);
        }

        if(isset($requestData['date_start']) && isset($requestData['date_end']) && $requestData['date_start']!=='' && $requestData['date_end']!==''){
            $entry->whereBetween('created_at', array($requestData['date_start'].' 00:00:00', $requestData['date_end'].' 23:59:59'));
        }

        $datas = $entry->orderBy('id','desc')->paginate(10);

        $datas->transform(function($v){
            $hobbyArray = json_decode($v->hobby);
            $hobbyResult = [];
            for($i=0;$i<count($hobbyArray);$i++){
                array_push($hobbyResult,TestData::$hobbyGroup[$hobbyArray[$i]]);
            }
            $v->hobby = implode('，',$hobbyResult);
            return $v;
        });

        return $this->get_view('v1.admin.admin.platform.index',compact('datas'));
    }

    //新增页模板
    public function create()
    {
        return $this->get_view('v1.admin.admin.platform.edit');
    }

    //编辑页模板
    public function edit($id)
    {
        $data  = TestData::find($id);
        $data->hobby = json_decode($data->hobby,true);
        return $this->get_view('v1.admin.admin.platform.edit',compact('data'));
    }
}
