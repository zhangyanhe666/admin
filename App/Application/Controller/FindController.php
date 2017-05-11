<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
use Library\Db\Sql\Predicate\Expression;
use Application\Tool\Html;
class FindController extends PublicController{
    public $types   =   array(
            'banner'=>'find_banner',
            'bigbanner'=>'find_banner',
            'choice'=>'find_banner',
            'tips'=>'find_banner',
            'font'=>'find_banner',
            'recommend'=>'find_video',
            'recommendSeize'=>'find_video',
            'videoList'=>'find_video',
            'roll'=>'find_video',
            'lattice'=>'find_video',
            'adBanner'=>'find_ad',
        );
    public $id;
    public $type;
    public function init() {
        parent::init();
        $this->type   =   $this->getRequest()->getQuery('type');
        $this->id     =   $this->getRequest()->getQuery('typeid');
        if(!empty($this->type) && !empty($this->id)){
            $this->changeTag($this->type,$this->id);
        }
    }

    //map=m<banner:banner图,choice:精选分类,tips:小提示,recommend:视频推荐,roll:滚动视频,lattice:田子视频,adBanner:广告
    public function indexAction() {
        //工具栏
        Html::addTool('add','添加');
        Html::addTool('tableconfig','表结构快速通道',array(
            'href'=>  $this->router()->url(array('control'=>'tableConfig','action'=>'edit'),array('menuId'=>$this->router()->getMenuId())),
            'target'=>'_blank'
        ));
        if(!empty($this->getServer('Model\ChildMenu')->getMenu()->mem_url)){
             Html::addTool('clearCache','清理缓存',array('exec'=>0));
        }
        
        Html::addOption('edit','内容管理',array(
            'href'=>$this->router()->url(array('action'=>'editList'),array('typeid'=>'__id','type'=>'__type'))
        ));
        
        $time  =   date('Y-m-d H:i:s');
        //获取数据
        $items  = $this->selfTable()->order(array('switch desc','sort'))
                ->join('find_banner',new Expression('find_banner.layout_id=find_layout.id AND find_banner.switch=1 AND find_banner.start_time < "'.$time.'" AND find_banner.end_time > "'.$time.'"'),array(),'left')
                ->join('find_video',new Expression('find_video.layout_id=find_layout.id AND find_video.switch=1 AND find_video.start_time < "'.$time.'" AND find_video.end_time > "'.$time.'"'),array(),'left')
                ->join('find_ad',new Expression('find_ad.layout_id=find_layout.id AND find_ad.switch=1 AND find_ad.start_time < "'.$time.'" AND find_ad.end_time > "'.$time.'"'),array('count'=>new Expression(' COUNT(find_banner.id)+COUNT(find_video.id)+COUNT(find_ad.id)'),'min'=>new Expression(' concat_ws("",min(find_banner.end_time),min(find_video.end_time),min(find_ad.end_time))')),'left')
                ->queryColumns()->joinTable()->group('find_layout.id')->getAll()->toArray();
        $this->tableConfig()->addColumn('count',array('comment'=>'总记录数'));
        $this->tableConfig()->addColumn('min',array('comment'=>'最早下线时间'));
       
        $this->viewData()->setVariable('items', $items);//获取数据
        $this->viewData()->setVariable('param', array());
        
    }
    public function editListAction(){
        //工具栏
        Html::addTool('addItem','添加',array(
            'href'=>$this->router()->url(array('action'=>'addItem'),array('typeid'=>$this->id,'type'=>$this->type)),
        ));
        Html::addTool('tableconfig','表结构快速通道',array(
            'href'=>  $this->router()->url(array('control'=>'tableConfig','action'=>'edit'),array('menuId'=>0,'table'=>$this->tableConfig()->getTable())),
            'target'=>'_blank'
        ));
        if(!empty($this->getServer('Model\ChildMenu')->getMenu()->mem_url)){
             Html::addTool('clearCache','清理缓存',array('exec'=>0));
        }
        
        Html::addOption('edit','编辑',array(
            'href'=>$this->router()->url(array('action'=>'editItem'),array('typeid'=>$this->id,'type'=>$this->type,'id'=>'__id'))
        ));
        Html::addOption('delete','删除',array('onclick'=>  'admin.deleteItem(__id);'));
        
        $date   =   $this->getRequest()->getQuery('date',date('Y-m-d H:i:s'));
        $items  =   $this->selfTable()->where(array('layout_id'=>$this->id))->order(array('switch desc','sort'))->queryColumns()->joinTable()->getAll()->toArray();
        $n      =   date('N',  strtotime($date));
        if(isset($items[0]['week_type'])){
            uasort($items, function($v1,$v2) use($n){
                if($v1['switch'] != $v2['switch']){
                    return 1;
                }
                $s1 =   strpos($v1['week_type'],$n) === FALSE;
                $s2 =   strpos($v2['week_type'],$n) === FALSE;
                if ($s1 == $s2) {
                    return $v1['sort'] > $v2['sort'] ? 1 : -1;
                }
                return $s1 ? 1 : -1;
            });
        }
        $this->viewData()->setVariable('items', $items);
        $this->viewData()->setVariable('param', array('typeid'=>$this->id,'type'=>$this->type));
        $this->viewData()->setVariable('showtime','find_video' == $this->types[$this->type]);
        $this->viewData()->addTpl('find/index');
    }
    public function editItemAction() {
        parent::editAction();
        $this->viewData()->setVariable('submitAction',  $this->router()->url(array('action'=>'doEdit?typeid='.$this->id.'&type='.$this->type)));
    }
    public function doEditAction() {
        $router     =   $this->getRequest()->getPost('router');
        $routerValue=   $this->getRequest()->getPost('routerValue');
        $cover      =   $this->getRequest()->getPost('cover');
        if($router == '31' && !empty($routerValue) && empty($cover)){
            $video  = $this->selfModel('v_all')->where(array('wkid'=>$routerValue))->getRow();
            $this->getRequest()->getPost()->cover   =   $video->cover;
        }
        parent::doEditAction();
    }
    public function doAddAction() {
        $router     =   $this->getRequest()->getPost('router');
        $routerValue=   $this->getRequest()->getPost('routerValue');
        $cover      =   $this->getRequest()->getPost('cover');
        if($router == '31' && !empty($routerValue) && empty($cover)){
            $video  = $this->selfModel('v_all')->where(array('wkid'=>$routerValue))->getRow();
            $this->getRequest()->getPost()->cover   =   $video->cover;
        }
        parent::doAddAction();
    }
    public function addItemAction(){
        $this->viewData()->setVariable('submitAction',  $this->router()->url(array('action'=>'doAdd?typeid='.$this->id.'&type='.$this->type)));
        $this->viewData()->addTpl('lib/add');
    }
    public function changeTag($tag,$id){
        $tag    =   $this->types[$tag];
        $db     =   $this->tableConfig()->getDbServer()->dbKey();
        $this->tableConfig()->setMenuId(0)->setTable($db.'.'.$tag);
        $this->tableConfig()->getColumnList()->layout_id['viewType'] =   'sign';
        $this->tableConfig()->getColumnParam('layout_id')->default =   $id;
       
    }
    public function sortAction(){
        $sort   =   $this->getRequest()->getPost('sort');
        if(!empty($sort) && is_array($sort)){
            foreach ($sort as $k=>$v){
                $this->selfTable()->update(array('sort'=>$k),array('id'=>$v['id']));
            }
        }
    }
}
