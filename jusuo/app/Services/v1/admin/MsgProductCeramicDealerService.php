<?php
/**
 * Created by PhpStorm.
 * User: libin
 * Date: 2019/10/8
 * Time: 14:00
 */

namespace App\Services\v1\admin;


use App\Models\AdministratorDealer;
use App\Models\MsgAccountDealer;
use App\Models\MsgProductCeramicDealer;

class MsgProductCeramicDealerService
{

    private $dealer_id;
    private $type;
    private $content;

    public function add_msg()
    {
        $msg = new MsgProductCeramicDealer();
        $msg->dealer_id = $this->dealer_id;
        $msg->type = $this->type;
        $msg->content = $this->content;
        $result = $msg->save();
        return $result;
    }

  
    public function getDealerId()
    {
        return $this->dealer_id;
    }

    /**
     * @param mixed $dealer_id
     */
    public function setDealerId($dealer_id)
    {
        $this->dealer_id = $dealer_id;
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