<?php

namespace App\Services\v1\site;

use App\Http\Services\common\StrService;
use App\Models\Album;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DesignerSpace;
use App\Models\DesignerStyle;
use App\Models\DetailDealer;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\Space;
use App\Models\Style;
use Illuminate\Support\Facades\DB;

class DesignerService
{
    public static function getDesignerByCity($params){
        //$brand_scope, $cityId, $isOrderByFocus=true, $skip=0, $take=6
        $default = [
            'requestDesigner'=>isset($params['requestDesigner'])?$params['requestDesigner']:null,
            'brand_scope'=>'',
            'cityId'=>0,
            'isOrderByFocus'=>false,
            'skip'=>0,
            'take'=>6,
        ];
        $params = array_merge($default, $params);
        $city = Area::find($params['cityId']);
        $cityName = '';
        $brandId = $params['brand_scope'];
        if($brandId>0){
            $requestDesigner = $params['requestDesigner'];
            if($requestDesigner==null){
                return [];
            }
            //die(\GuzzleHttp\json_encode($requestDesigner->organization_type));
            if($requestDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //品牌设计师可见性
                //所有品牌设计师+旗下销售商的所有设计师
                $designer = \DB::table('designer_details as dd')
                    ->leftJoin('designers as d','dd.designer_id','=','d.id')
                    //对于品牌站，通过方案状态筛选出正确的设计师列表
                    ->where(function($query1)use($brandId){
                        $query1->where(function($brand_designer)use($brandId){
                            //品牌设计师，则筛选有已审核通过的方案的
                            $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                            $brand_designer->where('organization_id',$brandId);
                            $brand_designer->whereExists(function ($query) {
                                //品牌设计师筛选审核通过的方案
                                $query->select(DB::raw(1))
                                    ->from('albums')
                                    ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS);
                            });
                        });
                        $query1->orWhere(function($seller_designer)use($brandId){
                            //旗下销售商设计师，则筛选有品牌站可用的方案的
                            $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                            $seller_designer->whereExists(function ($query) use($brandId) {
                                //通过销售商关联品牌id
                                $query->select(DB::raw(1))
                                    ->from('organization_dealers as od')
                                    ->whereRaw('od.id = d.organization_id and od.p_brand_id = '.$brandId);
                            });
                            $seller_designer->whereExists(function ($query)use($brandId) {
                                //销售商设计师筛选品牌站可用的方案
                                $query->select(DB::raw(1))
                                    ->from('albums')
                                    ->whereRaw('albums.designer_id = d.id and albums.status = '.Album::STATUS_PASS.' and albums.status_brand = '.$brandId);
                            });

                        });
                    })
                    ->where('d.status',Designer::STATUS_ON);
            }else if($requestDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //销售商直属设计师可见性

            }

        }
        else{
            $designer = \DB::table('designer_details as dd')
                ->leftJoin('designers as d','dd.designer_id','=','d.id')
                ->where('d.status',Designer::STATUS_ON);
        }
        if($city) {
            $designer->where(['dd.area_serving_city'=>$params['cityId']]);
            $cityName = $city->shortname;
        }

        $designer->orderBy('dd.top_status','desc');
        if($params['isOrderByFocus']){
            $designer->orderBy('dd.point_focus','desc');
        }
        $designer->orderBy('dd.point_experience','desc');
        $designer->orderBy('dd.designer_id','desc');
        $designer = $designer->skip($params['skip'])
            ->take($params['take'])
            ->get(['designer_id','web_id_code','nickname','url_avatar','self_working_year',
                'area_serving_city','approve_realname','top_status','self_organization']);
        $designer->transform(function($v) use($cityName){
            if($cityName=='') {
                $city = Area::find($v->area_serving_city);
                if ($city)
                    $v->city = $city->shortname;
            }
            else{
                $v->city = $cityName;
            }
            $v->self_working_year = StrService::str_num_to_char($v->self_working_year).'年';
            $v->url_avatar = url($v->url_avatar);
            $v->identity = $v->approve_realname==DesignerDetail::APPROVE_REALNAME_YES?true:false;
            $v->hot = $v->top_status==DesignerDetail::TOP_STATUS_YES?true:false;
            return $v;
        });
        return $designer;
    }

    public static function getDesignerByOrganization($params){
        $organizationId = $params['organizationId'];
        $organizationType = $params['organizationType'];
        $isOrderByFocus = isset($params['isOrderByFocus'])?true:false;
        $isOrderByTopDealer = isset($params['isOrderByTopDealer'])?true:false;
        $isTop = isset($params['isTop'])?true:false;
        $skip = isset($params['skip'])?$params['skip']:0;
        $take = isset($params['take'])?$params['take']:5;
        $designer = \DB::table('designer_details as dd')
            ->leftJoin('designers as d','dd.designer_id','=','d.id')
            ->where([
                'd.organization_type'=>$organizationType,
                'd.organization_id'=>$organizationId,
                'd.status'=>Designer::STATUS_ON
            ])
            ->where('dd.count_album','>',0);//不显示没有上传设计方案的设计师
        if($isOrderByTopDealer){
            $designer->orderBy('d.top_dealer_status','desc');
            $designer->orderBy('d.top_dealer_time','desc');
        }
        if($isOrderByFocus){
            $designer->orderBy('dd.point_focus','desc');
        }
        $designer->orderBy('dd.top_status','desc');
        $designer->orderBy('dd.point_experience','desc');
        $designer->orderBy('dd.designer_id','desc');
        if($isTop){
            $designer->where('top_dealer_status',Designer::TOP_DEALER_STATUS_ON);
        }
        $total = $designer->count();
        $designer = $designer->skip($skip)
            ->take($take)
            ->get(['dd.designer_id','dd.nickname','d.web_id_code','d.top_dealer_status',
                'dd.url_avatar','dd.count_album','dd.self_designer_level']);
        $designer->transform(function($v) use($total){
            $v->total = $total;

            $v->url_avatar = url($v->url_avatar);
            $v->title = Designer::designerTitle($v->self_designer_level);
            return $v;
        });
        return $designer;
    }

    public static function getDesignerStyleString($designerId){
        $designerStyle = DesignerStyle::where('designer_id',$designerId)->pluck('style_id')->all();
        $str = '';
        $style = Style::pluck('name','id');
        foreach($designerStyle as $v){
            if($v<count($style)&&$style[$v]<>'') {
                if ($str <> '')
                    $str .= '，';
                $str .= $style[$v];
            }
        }
        return $str;
    }

    public static function getDesignerSpaceString($designerId){
        $designerSpace = DesignerSpace::where('designer_id',$designerId)->pluck('space_id')->all();
        $str = '';
        $space = Space::pluck('name','id');
        foreach($designerSpace as $v){
            if($v<count($space)&&$space[$v]<>'') {
                if ($str <> '')
                    $str .= '，';
                $str .= $space[$v];
            }
        }
        return $str;
    }

    public static function getDesignerBrandString($designerId){
        $designerBrandId = self::getDesignerBrandScope($designerId);
        if($designerId<=0){
            return '';
        }
        $brand = OrganizationBrand::find($designerBrandId);
        if(!$brand){
            return '';
        }
        return $brand->short_name;
    }

    public static function addToSearch(){
        /*$designer = Designer::pluck('id')->all();
        $count = 0;
        foreach($designer as $d){
            $albumCount = Album::where([
                'designer_id'=>$d,
                'visible_status'=>Album::VISIBLE_STATUS_ON,
            ])->count();
            DesignerDetail::where('designer_id',$d)->update([
                'count_album'=>$albumCount
            ]);
            $count++;
        }*/
        $web_id_code_count = StrService::str_table_field_unique('designers');
        return $web_id_code_count;
    }

    //设计师系统编号生成
    /**
     * @return string
     */
    public static function get_sys_code()
    {
        $str = date('Ymd');

        //随机识别码6位
        $random_code = str_pad(random_int(100000,999999),5,0,STR_PAD_LEFT);
        $str.= $random_code;

        $exist = Designer::query()->where('sys_code',$str)->first();
        if($exist){
            return self::get_sys_code();
        }

        return $str;

    }

    //判断设计师是否在品牌域内
    public static function isDesignerInBrandScope($designer_id,$brand_id)
    {
        return self::getDesignerBrandScope($designer_id)==$brand_id?true:false;
    }

    //获取设计师的品牌域
    public static function getDesignerBrandScope($designer_id)
    {
        $designer = Designer::find($designer_id);
        if(!$designer){
            return -1;
        }

        switch($designer->organization_type){
            case Designer::ORGANIZATION_TYPE_NONE:
                //自由设计师，0
                return 0;
                break;
            case Designer::ORGANIZATION_TYPE_BRAND:
                return $designer->organization_id;
                break;
            case Designer::ORGANIZATION_TYPE_SELLER:
                $seller = OrganizationDealer::find($designer->organization_id);
                if(!$seller){
                    return -1;
                }
                return $seller->p_brand_id;
                break;
            default:
                return -1;
                break;
        }
    }

    //检查设计师是否归属某销售商
    public static function checkDesignerDealer($designer_id, $dealer_id)
    {
        $designer = Designer::find($designer_id);
        if(!$designer){
            return -1;
        }

        switch($designer->organization_type){
            case Designer::ORGANIZATION_TYPE_NONE:
                //自由设计师，0
                return -1;
                break;
            case Designer::ORGANIZATION_TYPE_BRAND:
                return -1;
                break;
            case Designer::ORGANIZATION_TYPE_SELLER:
                if($designer->organization_id==$dealer_id){
                    return 1;
                }
                $seller = OrganizationDealer::find($designer->organization_id);
                if(!$seller){
                    return -1;
                }
                if($seller->pid==$dealer_id){
                    return 1;
                }
                break;
            default:
                return -1;
                break;
        }
    }

    public static function getDesignerBelongArea($designerId){
        $cityStringShort = '';
        $provinceStringShort = '';

        $designer = Designer::find($designerId);
        if(!$designer)
            return '';

        $organization = null;
        switch($designer->organization_type){
            case Designer::ORGANIZATION_TYPE_BRAND:
                $organization = OrganizationBrand::find($designer->organization_id);
                if(!$organization)
                    return '';
                else
                    return '全国';
                break;
            default:
                $organization = OrganizationDealer::find($designer->organization_id);
                while($organization&&$organization->p_dealer_id<>0){
                    $organization = OrganizationDealer::find($organization->p_dealer_id);
                }
                if(!$organization)
                    return '';
                $dealer = DetailDealer::where('dealer_id',$organization->id)->first();
                if(!$dealer)
                    return '';
                $cityId = $dealer->area_serving_city;
                $cityId = explode('|',$cityId,3);
                $cityId = $cityId[1];
                $city = Area::find($cityId);
                if($city&&$city->level==2){
                    $cityStringShort = $city->shortname;
                    $province = Area::find($city->pid);
                    if($province&&$province->level==1) {
                        $provinceStringShort = $province->shortname;
                    }
                }
                return $provinceStringShort.'/'.$cityStringShort;
                break;
        }
    }

    public static function getDesignerBelongOrganizationName($designer){
        $organization = null;
        switch($designer->organization_type){
            case Designer::ORGANIZATION_TYPE_BRAND:
                $organization = OrganizationBrand::find($designer->organization_id);
                if(!$organization)
                    return '';
                else
                    return $organization->name;
                break;
            default:
                $organization = OrganizationDealer::find($designer->organization_id);
                if(!$organization)
                    return '';
                else
                    return $organization->short_name;
                break;
        }
    }

    public static function getDesignerBelongOrganizationNameCode($designer){
        $organization = null;
        switch($designer->organization_type){
            case Designer::ORGANIZATION_TYPE_BRAND:
                $organization = OrganizationBrand::find($designer->organization_id);
                if(!$organization)
                    return [
                        'name'=>'',
                        'code'=>'',
                    ];
                else
                    return [
                        'name'=>$organization->name,
                        'code'=>'',
                    ];
                break;
            default:
                $organization = OrganizationDealer::find($designer->organization_id);
                if(!$organization)
                    return [
                        'name'=>'',
                        'code'=>'',
                    ];
                else
                    return [
                        'name'=>$organization->short_name,
                        'code'=>$organization->web_id_code,
                    ];
                break;
        }
    }

}