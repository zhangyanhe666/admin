<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller;

use Application\Base\PublicController;


class VerificationcodeController extends PublicController{
    
    
    public function addAction() {
        $this->tableConfig()->setColumnAttr('code',array('processor' =>'hidden'));
        parent::addAction();
    }
    public function doAddAction() {
        $this->getRequest()->getPost()->code    =   substr(md5($this->getRequest()->getPost('online')),0,8);
        parent::doAddAction();
    }
}