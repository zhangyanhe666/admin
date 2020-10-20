<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller;
use Library\Application\Common;
use Application\Tool\User;
use Application\Tool\Authority;
class AdminUserController extends AdmingroupController
{
    //编辑页
    public function editAction() {
        parent::editAction();
        $uid    =   $this->getRequest()->getQuery('id');
        $this->viewData()->setVariable('checkList',  Authority::authSplit($this->getService('Model\AdminUser')->getUserAuth($uid)) );
  }
    public function doUsercenterAction(){        
        $this->selfTable()->edit($this->getRequest()->getPost('id'),$this->tplFormat()->doEdit());
    }
    public function usercenterAction(){
        $item =  $this->selfTable()->getItem(User::userInfo()->id);
        $this->tableConfig()->getColumnList()->login_num['viewType'] =   'notUse';
        $this->tableConfig()->getColumnList()->group_id['viewType'] =   'notUse';
        $this->tableConfig()->getColumnList()->isdisable['viewType'] =   'notUse';
        $this->viewData()->setVariable('submitAction',  $this->getService('router')->url(array('action'=>'doUsercenter')));  
        $this->viewData()->setVariable('item',  $item);
        $this->viewData()->addTpl('lib/edit');
    }
}
