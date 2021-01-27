<?php

namespace App\Http\Controllers\v1\mobile\news;

use App\Http\Controllers\v1\VersionController;
use App\Models\CeramicApplyCategory;
use App\Models\Designer;
use App\Models\NewsBrand;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewsController extends VersionController
{
    //
    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function detail($web_id_code,Request $request)
    {


        $news = NewsBrand::where('web_id_code',$web_id_code)->first();

        if(!$news){
            return $this->goTo404();
        }


        return $this->get_view('v1.mobile.news.detail',compact('news'));
    }




}
