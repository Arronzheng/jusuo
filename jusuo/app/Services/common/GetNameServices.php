<?php


namespace App\Http\Services\common;


use App\Models\AdministratorBrand;
use App\Models\AdministratorDealer;
use App\Models\AdministratorOrganization;
use App\Models\AdministratorPlatform;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerAccountType;
use App\Models\DesignerDetail;
use App\Models\DetailBrand;
use App\Models\DetailDealer;
use App\Models\Member;
use App\Models\Organization;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;

/**
 * Class GetNameServices
 * @package App\Http\Services\common
 */
class GetNameServices
{

    const TYPE_PLATFORM_ADMIN = 0;
    const TYPE_BRAND_ADMIN = 1;
    const TYPE_SELLER_ADMIN = 2;
    const TYPE_DESIGNCOMPANY_ADMIN = 3;

    /* ---------------获取各组织的组织码 start------------------ */
    public function getBrandIdCode()
    {
        $code = '';
        $organization_id = OrganizationBrand::query()->max('id');
        if (!$organization_id){
            $code = $this->getOrganizationStart(OrganizationService::ORGANIZATION_TYPE_BRAND);
        }else{
            $organization = OrganizationBrand::find($organization_id);
            $code = (int)$organization->organization_id_code;
        }

        $code += 1;

        //数字4位，不够则补0
        $code = sprintf("%04d",$code);

        return $code;
    }

    public function getSellerIdCode($brand_id)
    {
        //获取经销商序号
        $code = '';
        $organization_count = OrganizationDealer::query()->where('p_brand_id',$brand_id)->count();
        $code = $organization_count+1;

        //数字2位，不够则补0
        $code = sprintf("%02d",$code);

        return $code;
    }

    protected function getOrganizationStart($organization_type)
    {
        $start = '0100';
        switch ($organization_type){
            case OrganizationService::ORGANIZATION_TYPE_BRAND:
                break;
            case OrganizationService::ORGANIZATION_TYPE_DESIGN_COMPANY:
                //城市区号 不足4位则前面补0
                break;
            case OrganizationService::ORGANIZATION_TYPE_SELLER:
                break;
        }
        return $start;
    }

    /* ---------------获取各组织的组织码 end------------------ */


    /* ---------------获取各组织的组织账号 start---------------------*/
    public function getBrandAccountName($brand_id, $product_category_id)
    {
        $name = 'P';

        //根据经营品类获取经营品类码
        $name .= $this->getBusinessCode($product_category_id);
        //品牌序号
        $organization = OrganizationBrand::find($brand_id);
        $name .= $organization->organization_id_code;

        return $name;
    }
    public function getSellerAccountName($seller_id)
    {
        $name = 'S';

        $seller = OrganizationDealer::find($seller_id);
        $brand = OrganizationBrand::find($seller->p_brand_id);
        //经营品类码
        $business_code = $this->getBusinessCode($brand->product_category,'seller');
        $name .= $business_code;
        //品牌序号
        $brand_code = $brand->organization_id_code;
        $name .= $brand_code;
        //服务城市区号4位，不足4位前面补0
        $area_code = '0';
        $seller_area = Area::find($seller->detail->area_serving_id);
        if($seller_area){
            $area_code = $seller_area->code;
        }
        $area_code = sprintf("%04d",$area_code);
        $name .= $area_code;
        //经销商序号（2位，01起）
        $name .= $seller->organization_id_code;
        return $name;
    }
    /* ---------------获取各组织的组织账号 end  ---------------------*/


    public function getDesignerAccount($belong_organization_type=0,$organization_id=0, $self_designer_type=0)
    {
        $account_name = 'D';

        $account_name .= $this->belong_organization_type($belong_organization_type);

        //城市区号4位，不足4位前面补0
        $organizationDetail = null;
        $area_code = '0';
        switch ($belong_organization_type){
            case OrganizationService::ORGANIZATION_TYPE_BRAND:
                $organizationDetail = DetailBrand::where('brand_id',$organization_id)->first();
                if($organizationDetail){
                    $organization_area = Area::find($organizationDetail->area_belong_id);
                    if($organization_area){
                        $area_code = $organization_area->code;
                    }
                }
                break;
            case OrganizationService::ORGANIZATION_TYPE_SELLER:
                $organizationDetail = DetailDealer::where('dealer_id',$organization_id)->first();
                if($organizationDetail){
                    $organization_area = Area::find($organizationDetail->area_serving_id);
                    if($organization_area){
                        $area_code = $organization_area->code;
                    }
                }
                break;
            case OrganizationService::ORGANIZATION_TYPE_DESIGN_COMPANY:
                //待开发
                //$organization = //
                break;
        }

        $area_code = sprintf("%04d",$area_code);

        $account_name .= $area_code;

        $account_name .= $this->designerType($self_designer_type);

        $account_name .= $this->getDesignerIdCode();

        $account_name .= random_int(10,99);

        return $account_name;
    }

    /*-----------------获取各组织管理员账号id start-------------------*/

    public function getPlatformAdminAccountName($level)
    {
        $name = 'ADMIN';
        $name .= $level;
        $admin_count = AdministratorPlatform::count();
        $adminNumber = $admin_count+1;
        $adminNumber = sprintf("%03d",$adminNumber);
        $name .= $adminNumber;

        $name .=  random_int(0,9);
        return $name;
    }

    public function getBrandAdminAccountName($brand_id ,$level, $product_category_id)
    {
        /*$name = 'P';

        //根据经营品类获取经营品类码
        $name .= $this->getBusinessCode($product_category_id);
        //品牌序号
        $organization = OrganizationBrand::find($brand_id);
        $name .= $organization->organization_id_code;*/

        $brand = OrganizationBrand::find($brand_id);

        $name = $brand->organization_account;

        //管理员级别
        $name .= $level;

        //管理员序号
        $admin_count = AdministratorBrand::OfBrandId($brand_id)->count();
        $adminNumber = $admin_count+1;
        $adminNumber = sprintf("%03d",$adminNumber);
        $name .= $adminNumber;

        //随机码
        $name .=  random_int(0,9);
        return $name;
    }

    public function getSellerAdminAccountName($seller_id,$level)
    {
        $name = 'S';

        $seller = OrganizationDealer::find($seller_id);
        $brand = OrganizationBrand::find($seller->p_brand_id);
        //经营品类码
        $business_code = $this->getBusinessCode($brand->product_category,'seller_admin');
        $name .= $business_code;
        //品牌序号
        $brand_code = $brand->organization_id_code;
        $name .= $brand_code;
        //城市区号4位，不足4位前面补0
        $area_code = '0';
        $seller_area = Area::find($seller->detail->area_serving_id);
        if($seller_area){
            $area_code = $seller_area->code;
        }
        $area_code = sprintf("%04d",$area_code);
        $name .= $area_code;
        //经销商序号（2位，01起）
        $name .= $seller->organization_id_code;
        //管理级别
        $name .=$level;
        //管理员序号
        $admin_count = AdministratorDealer::OfDealerId($seller->id)->count();
        $adminNumber = $admin_count+1;
        $adminNumber = sprintf("%03d",$adminNumber);
        $name .= $adminNumber;

        //随机码
        $name .=  random_int(0,9);
        return $name;
    }

    protected function getDesignCompanyAdminName($organization_id,$level)
    {
        $name = 'D';
        //保留位
        $x = chr(rand(65,90));
        while ($x == 'R'|| $x=='P'|| $x=='S'|| $x=='D'){
            $x = chr(rand(65,90));
        }
        $name .=$x;

        //装饰公司序号
        $organization = Organization::with('detail')->find($organization_id);
        $name .= $organization->detail->organization_id_code;
        //管理级别
        $name .= $level;

        $admin_id = AdministratorOrganization::OfOrganizationId($organization_id)->max('id');
        if (!$admin_id){
            $adminNumber = '1';
        }else{
            $admin = AdministratorOrganization::find($admin_id);
            $adminNumber = (int)substr($admin->login_username,3,3);
            $adminNumber += 1;
        }
        $name .= $adminNumber;
        //随机码
        $name .=  random_int(0,9);
        return $name;
    }

    /*-----------------获取各组织管理员账号id end-------------------*/


    //获取经营品类码
    protected function getBusinessCode($productCategoryId,$codeType='brand')
    {
        $business_code = '';
        $productCat = ProductCategory::find($productCategoryId);
        if($productCat){
            switch($codeType){
                case 'brand':
                    $business_code = $productCat->brand_code;
                    break;
                case 'brand_admin':
                    $business_code = $productCat->brand_admin_code;
                    break;
                case 'seller':
                    $business_code = $productCat->seller_code;
                    break;
                case 'seller_admin':
                    $business_code = $productCat->seller_admin_code;
                    break;
                default:break;
            }

        }
        return $business_code;
    }


    protected function belong_organization_type($belong_organization_type)
    {
        $str = 'R';
        switch ($belong_organization_type){
            case OrganizationService::ORGANIZATION_TYPE_BRAND:
                $str ='P';
                break;
            case OrganizationService::ORGANIZATION_TYPE_SELLER:
                $str ='S';
                break;
            case OrganizationService::ORGANIZATION_TYPE_DESIGN_COMPANY:
                $str ='D';
                break;
        }

        return $str;

    }

    protected function designerType($self_designer_type)
    {
        $str = 'Z';
        $designer_account_type = DesignerAccountType::find($self_designer_type);
        if($designer_account_type){
            if($designer_account_type->name_char){
                $str = $designer_account_type->name_char;
            }else{
                $str = 'Z';
            }
        }

        return $str;

    }

    public function getDesignerIdCode()
    {
        $member_id = Designer::max('id');
        $member = Designer::find($member_id);
        if (!$member){
            $designerNumber = '0101';
            return $designerNumber;
        }

        $designerNumber = (int)$member->designer_id_code;
        $designerNumber += 1;
        $designerNumber = sprintf("%04d",$designerNumber);
        return $designerNumber;
    }
}