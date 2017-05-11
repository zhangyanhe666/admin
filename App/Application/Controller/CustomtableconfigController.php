<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller;
use Application\Base\PublicController;
use Application\Tool\Html;
class CustomtableconfigController extends PublicController{
    
    public function indexAction(){
        parent::indexAction();
        Html::addOption('copyfromtext', '使用测试配置',array('exec'=>0));
    }
    public function copyfromtextAction(){
        $id     =   $this->getRequest()->getQuery('id');
        if($item = $this->selfTable()->getItem($id)){
            $table  =   str_replace('wukong','wukong214',$item->table_name);
            if($item->table_name != $table){
                $count  =   $this->selfTable()->where(array('table_name'=>$table))->getColumn('count(*)');
                if($count == 1){
                    $testItem   =   $this->selfTable()->where(array('table_name'=>$table))->getRow();
                    $nowconfig  =   json_decode($item->config);
                    $config =   json_decode($testItem->config);
                    $config->tableInfo->createTime  =   $nowconfig->tableInfo->createTime;
                    $this->selfTable()->edit($id,array('config'=>json_encode($config)));
                    return $this->responseSuccess();  
                }
            }
        }
        return $this->responseError('操作失败');
    }
}