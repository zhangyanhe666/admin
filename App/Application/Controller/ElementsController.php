<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Library\Application\Common;
use Application\Base\PublicController;
class ElementsController extends PublicController{

    public function doAddAction()
    {
        //这个地方请教艳鹤，看看zend中怎么写
        $maxids = $this->selfTable()->columns(array("id"))->order('id desc')->limit(1)->getAll()->toArray();
        $addDatas = $this->tplFormat()->doAdd();
        if (empty($addDatas['id'])) {
            if (empty($maxids)) {
                $addDatas['id'] = 1;
            } else {
                $addDatas['id'] = $maxids[0]['id'] + 1;
            }
        }
        $this->selfTable()->add($addDatas);
    }

    public function addAction(){
        $this->viewData()->addTpl('dailyup/add');
    }

}




//在编辑的时候，