<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller;
use Application\Base\PublicController;
use Library\Application\Common;
use Application\Tool\Authority;
class AdmingroupController extends PublicController
{
    //添加页
    public function addAction() {
        parent::addAction();
        $this->viewData()->setVariable('checkBoxTitle',  '数据库列表');
        $this->viewData()->setVariable('checkBoxList',  $this->getServer('Model\ChildMenu')->getMenuList());
        $this->viewData()->addTpl('lib/checkBoxList');
    }
    //编辑页
    public function editAction() {
        parent::editAction();
        $gid    =   $this->getRequest()->getQuery('id');
        $this->viewData()->setVariable('checkBoxTitle',  '数据库列表');
        $this->viewData()->setVariable('checkBoxList',  $this->getServer('Model\ChildMenu')->getMenuList());
        $this->viewData()->setVariable('checkedList', Authority::authSplit($this->getServer('Model\AdminGroup')->getGroupAuth($gid)));
        $this->viewData()->addTpl('lib/checkBoxList');
    }
    //添加操作
    public function doAddAction(){
        parent::doAddAction();
        $this->selfTable()->addAuthMap($this->selfTable()->getLastInsertValue(),Authority::authMerge($this->getRequest()->getPost('__check')));
    }
    //编辑操作
    public function doEditAction() {
        parent::doEditAction();
        $id             =   $this->getRequest()->getPost('id');
        $this->selfTable()->delAuthMap($id);
        $this->selfTable()->addAuthMap($id,Authority::authMerge($this->getRequest()->getPost('__check')));
    }
    public function deleteAction() {
        parent::deleteAction();
        $id             =   $this->getRequest()->getPost('id');
        $this->selfTable()->delAuthMap($id);
    }

}
