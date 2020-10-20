<?php

namespace Application\Controller;
use Application\Base\PublicController;
use Application\Tool\Html;
use Library\Application\Common;
class SpecialjsonController extends PublicController
{
    
    public function indexAction(){
        parent::indexAction();
        Html::addOption('tomorespecial', '迁',array('exec'=>0));
        Html::addOption('clearSpecialCache', '清',array('exec'=>0));
        Html::addOption('openHtml', '阅',
                array(
                    'href'=>  $this->router()->url(array('action'=>'openHtml'),array('id'=>'__id','type'=>'__type')),
                    'target'=>'_blank'
                    )
                );
        Html::addOption('editvote', '票');
        $voteId = $this->getService('Model\ChildMenu')->where(array('table_name'=>$this->selfTable()->dbKey().'.special_vote_option'))->getRow()->id;
        Html::addOption('editoption', '票项',
                array(
                    'href'=>  $this->router()->url(array('control'=>'dispatch_'.$voteId),array('sign'=>'__id')),
                )
                );
    }
    public function openHtmlAction(){
        $id     =   $this->getRequest()->getQuery('id');
        $type   =   $this->getRequest()->getQuery('type');
        $url    =   $this->specialUrl($type);
        $this->router()->toUrl($url.$id);exit;
    }
    public function tomorespecialAction(){        
        $id         =   $this->getRequest()->getQuery('id');
        if(empty($id) || !($item    =   $this->selfTable()->getItem($id))){
             return $this->responseError('复制失败,联系管理员');
        }
        $link   =   $this->specialUrl($item->type);
      
        $data['name']       =   $item->title;
        $data['desc']       =   $item->desc;
        $data['cover']      =   $item->cover;
        $data['router']     =   30;
        $data['routerValue']=   $link.$id;
        $data['sort']       =   $this->selfModel('index_more_special')->getColumn('max(sort)')+1;
        $this->selfModel('index_more_special')->insert($data);
       return $this->responseSuccess();
    }
    public function clearSpecialCacheAction() {
        $id         =   $this->getRequest()->getQuery('id');
        $url        =   "http://api.wukongtv.com/special/viewdata?id={$id}&debug=1";
        file_get_contents($url);
       return $this->responseSuccess();
    }
    public function specialUrl($type){
        $url    =   '';
        //选择跳转模板
        switch ($type){
            case 0:
                $url    =   'http://static1.wukongtv.com/special/zhuantijson/video_detail_horizontal.html?id=';
            break;
            case 1:
                $url    =   'http://static1.wukongtv.com/special/zhuantijson/video_detail_vertical.html?id=';
            break;
            case 2:
                $url    =   'http://static1.wukongtv.com/special/zhuantijson/video_detail_scroll.html?id=';
            break;
            case 3:
                $url    =   'http://static1.wukongtv.com/special/zhuantijson/video_detail_zhibo.html?id=';
            break;
        }
        return $url;
    }
    public function editvoteAction(){
        $id =   $this->getRequest()->getQuery('id');
        if(empty($id)){            
            $this->router()->toUrl(Common::$error);
        }
        $db     =   $this->tableConfig()->getDbServer()->dbKey();
        $this->tableConfig()->setMenuId(0)->setTable($db.'.special_vote');
        $this->tableConfig()->setColumnAttr('special_id',array('viewType'=>'sign','default'=>$id));
        $this->tableConfig()->setColumnAttr('type',array('viewType'=>'select','param'=>'map=m<pk:pk类型,single:单选类型,multiterm:多选类型'));
        $this->viewData()->setVariable('submitAction',  $this->router()->url(array('action'=>'doEditvote')));
        $item =  $this->selfModel('special_vote')->where(array('special_id'=>$id))->getRow();
        if($item->count() == 0){
            $this->viewData()->addTpl('lib/add');
        }else{
            $this->viewData()->setVariable('item', $item);
            $this->viewData()->addTpl('lib/edit');
        }
    }
    public function doEditvoteAction(){
        $db     =   $this->tableConfig()->getDbServer()->dbKey();
        $this->tableConfig()->setMenuId(0)->setTable($db.'.special_vote');
        if(!empty($this->getRequest()->getPost('id'))){
            parent::doEditAction();
        }else{
            parent::doAddAction();
        }
    }

}