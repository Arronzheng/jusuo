<?php
/**
 * Created by PhpStorm.
 * User: libin
 * Date: 2019/10/8
 * Time: 14:00
 */

namespace App\Services\v1\admin;


use App\Models\AdministratorBrand;
use App\Models\MsgAccountBrand;
use App\Models\MsgProductCeramicBrand;

class MsgProductCeramicBrandService
{

    private $brand_id;
    private $administrator_id;
    private $type;
    private $content;

    public function add_msg()
    {
        $msg = new MsgProductCeramicBrand();
        $msg->brand_id = $this->brand_id;
        $msg->administrator_id = $this->administrator_id;
        $msg->type = $this->type;
        $msg->content = $this->content;
        $result = $msg->save();
        return $result;
    }

    /**
     * @return mixed
     */
    public function getAdministratorId()
    {
        return $this->administrator_id;
    }

    /**
     * @param mixed $brand_id
     */
    public function setAdministratorId($administrator_id)
    {
        $this->administrator_id = $administrator_id;
    }

    /**
     * @return mixed
     */
    public function getBrandId()
    {
        return $this->brand_id;
    }

    /**
     * @param mixed $brand_id
     */
    public function setBrandId($brand_id)
    {
        $this->brand_id = $brand_id;
    }


    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

}