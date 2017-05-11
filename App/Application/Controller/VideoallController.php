<?php

namespace Application\Controller;
use Application\Base\PublicController;
use Application\Tool\Html;
class VideoallController extends PublicController
{
    
    public function indexAction(){
        parent::indexAction();
        Html::addOption('changecover', '切换大图',array('exec'=>0));
        Html::addOption('clearCache', '清理缓存',array('exec'=>0));
    }
    public function clearCacheAction(){
       $id         =   $this->getRequest()->getQuery('id');
       $cacheUrl    =   'http://api.wukongtv.com/mem/showCacheKey?uri=/dianbo/detail4_'.$id;
       $jsondata   =   file_get_contents($cacheUrl);
       return $this->responseSuccess('更新完成');
    }
    public function doEditColumnAction() {
        if($this->getRequest()->getPost('black') !== ''){
            $status     =   $this->getRequest()->getPost('black') == 1 ? 0 : 1;
            $wkid =  $this->selfTable()->getItem($this->getRequest()->getPost('id'))->wkid;
            $this->selfModel('v_subtype')->update(array('status'=>$status),array('wkid'=>$wkid));
        }
       return parent::doEditColumnAction();
    }
    public function changecoverAction(){
        $id         =   $this->getRequest()->getQuery('id');
        if(empty($id) || !($item    =   $this->selfTable()->getItem($id))){
            return $this->responseError('切换大图失败');
        }
        if(strpos($item->cover,'douban') !== false){
            $cover      = 'http://img3.douban.com/view/photo/photo/public/'.strrev(strstr(strrev($item->cover),'/',true));
            $this->selfTable()->update(array('cover'=>$cover),array('id'=>$id));
        }else{            
            return $this->responseError('非豆瓣海报无法切换');
        }
        return $this->responseSuccess();
    }
    public function deleteAction() {
        $id     =   $this->getRequest()->getQuery('id');
        $item   =   $this->selfTable()->where(array('id'=>$id))->getRow();
        if(!empty($item)){
            $this->selfModel('v_subtype')->delete(array('wkid'=>$item->wkid));
            $this->selfModel('v_jingxuan')->delete(array('wkid'=>$item->wkid));
        }
        return parent::deleteAction();        
    }
}