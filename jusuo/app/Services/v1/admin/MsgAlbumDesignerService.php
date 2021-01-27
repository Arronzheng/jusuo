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
use App\Models\MsgAlbumDesigner;
use App\Models\MsgProductCeramicBrand;

class MsgAlbumDesignerService
{

    private $designer_id;
    private $type;
    private $content;

    public function add_msg()
    {
        $msg = new MsgAlbumDesigner();
        $msg->designer_id = $this->designer_id;
        $msg->type = $this->type;
        $msg->content = $this->content;
        $result = $msg->save();
        return $result;
    }


    /**
     * @return mixed
     */
    public function getDesignerId()
    {
        return $this->designer_id;
    }

    /**
     * @param mixed $brand_id
     */
    public function setDesignerId($designer_id)
    {
        $this->designer_id = $designer_id;
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