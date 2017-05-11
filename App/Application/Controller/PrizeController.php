<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
class PrizeController extends PublicController
{
    public function indexAction() {
        parent::indexAction();
        \Application\Tool\Html::delOption();
    }
            //添加
    public function doAddAction(){
        $nowTime        =   date('Y-m-d H:i:s');
        $where['id']    =   $this->getRequest()->getPost('act_id');
        $where[]        =   'start_time < "'.$nowTime.'" and end_time > "'.$nowTime.'"';
        $res            =   $this->selfModel('activity')->where($where)->getRow();
        if($res->count()>0){
            return $this->responseError('活动期间不能添加奖品');
        }
        return parent::doAddAction();
    }
}