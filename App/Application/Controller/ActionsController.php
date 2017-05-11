<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Library\Application\Common;
use Application\Base\PublicController;
class ActionsController extends PublicController{

    public function doAddAction()
    {
        //这个地方请教艳鹤，看看zend中怎么写
        $maxids = $this->selfTable()->columns(array("action_id"))->order('action_id desc')->limit(1)->getAll()->toArray();
        $addDatas = $this->tplFormat()->doAdd();
        if (empty($addDatas['action_id'])) {
            if (empty($maxids)) {
                $addDatas['action_id'] = 1;
            } else {
                $addDatas['action_id'] = $maxids[0]['action_id'] + 1;
            }
        }
        $this->selfTable()->add($addDatas);
    }



}




//在编辑的时候，