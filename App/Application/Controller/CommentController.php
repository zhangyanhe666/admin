<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller;
use Application\Base\PublicController;
use Library\Application\Common;
use Application\Tool\Html;
class CommentController extends PublicController
{
    public function indexAction() {
        parent::indexAction();
        Html::addTool('chart', '评论趋势图');
        Html::addOption('addFilterModel', '加入到过滤模板',array('exec'=>0));
    }
    public function chartAction(){
        $date   =   date('Y-m-d 00:00:00)',  strtotime('-30 day'));
        $linechart   = $this->selfTable()->columns(array(
            'value'=>new \Library\Db\Sql\Predicate\Expression('count(*)'),
            'date'=>new \Library\Db\Sql\Predicate\Expression('DATE_FORMAT(create_time,\'%Y-%m-%d\')'),
            'label'=>new \Library\Db\Sql\Predicate\Expression('DATE_FORMAT(create_time,\'%Y-%m-%d\')'),
        ))->group('date')->order('date')->where(array("create_time >= '{$date}'",'switch'=>0))->getAll()->toArray();
        $this->viewData()->setVariable('linechart',  json_encode($linechart));
    }
    public function addFilterModelAction(){
       $id         =   $this->getRequest()->getQuery('id');
       if(empty($id) || !($item    =   $this->selfTable()->getItem($id))){
            return $this->responseError('加入失败');
       }
       $info    =   array('model'=>$item->content);
       $this->selfModel('filter_comment_model')->insert($info);
       $this->selfTable()->filter($item->content,function($v) use($item){
           return Common::textModelFilter($v['content'], $item->content);
       });
        return $this->responseSuccess();
    }
}
