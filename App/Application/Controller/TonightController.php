<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
class TonightController extends PublicController{
    
    public function doEditColumnAction() {
        $this->checkTime();
        parent::doEditColumnAction();
    }
    public function doAddAction() {
        $this->checkTime();
        $cover              =   $this->getRequest()->getPost('cover');
        $subscribe_cover    =   $this->getRequest()->getPost('subscribe_cover');
        if(is_numeric($cover)){
            $this->getRequest()->getPost()->cover   =   $this->getCover($cover);
        }
        if(is_numeric($subscribe_cover)){
            $this->getRequest()->getPost()->subscribe_cover   =   $this->getCover($subscribe_cover);
        }
        parent::doAddAction();
    }
    public function doEditAction() {
        $this->checkTime();
        $cover              =   $this->getRequest()->getPost('cover');
        $subscribe_cover    =   $this->getRequest()->getPost('subscribe_cover');
        if(is_numeric($cover)){
            $this->getRequest()->getPost()->cover   =   $this->getCover($cover);
        }
        if(is_numeric($subscribe_cover)){
            $this->getRequest()->getPost()->subscribe_cover   =   $this->getCover($subscribe_cover);
        }
        parent::doEditAction();
    }
    private function getCover($id){
        return $this->selfModel('v_all')->where(array('wkid'=>$id))->getRow()->cover;
    }
    private function checkTime(){
        $starttime  =   $this->getRequest()->getPost('starttime');
        $endtime    =   $this->getRequest()->getPost('endtime');
        $checkstart =   strtotime(date('Y-m-d '.$starttime));
        $checkend   =   strtotime(date('Y-m-d '.$endtime));
        if(empty($checkstart)){
            throw new \Application\Exception\MsgException('开始时间填写错误，格式00:00,注意使用英文冒号');
        }
        if(empty($checkend)){
            throw new \Application\Exception\MsgException('结束时间填写错误，格式00:00,注意使用英文冒号');
        }
    }
}