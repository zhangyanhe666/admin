<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Library\Application\Common;
use Application\Base\PublicController;
use Application\Tool\Html;
class LessonActionController extends PublicController{

    public function indexAction() {
        parent::indexAction();
        Html::addTool('addLesson', '添加课程');
    }

    public function selectHtmlAction(){
        $res = $this->getServer('Tool\Tpl\TplFormat')->add(array("action_id"=>'0'));
        echo $res;die;
    }

    public function lessonAcsAction(){
        $lesson_id = $_GET['lesson_id'];
        $mode = $_GET['mode'];

        $res =  $this->selfTable()->where(array('lesson_id'=>$lesson_id,'mode'=>$mode))->order('sort asc')->getAll()->toArray();
        echo json_encode($res);
        exit();
    }

    //添加页处理方法
    public function add($item){
        $data   = $this->getServer('Tool\Tpl\TplFormat')->tplTool()->getItem(__FUNCTION__,$item,function($v){
            return htmlspecialchars($v);
        });
        $tpl    =   implode('', $data);
        return $tpl;
    }

    //添加整个课程中中的动作集合
    public function addLessonAction(){
        $this->viewData()->addTpl('dailyup/lessonActionAdd');
    }





    public function doAddLessonAcsAction(){

        $lesson_id = $_POST['lesson_id'];
        $mode = $_POST['mode'];
        $action_ids_post = empty($_POST['action_id'])?'':$_POST['action_id'];

        //查找对应关系和顺序。最好有

        if(empty($action_ids_post)){

            return;//犯错误信息
        }

        $haveActions =  $this->selfTable()->columns(array('lesson_id','action_id','sort','mode'))->where(array('lesson_id'=>$lesson_id,'mode'=>$mode))->getAll()->toArray();
        $haveActionDicts = array();

        foreach($haveActions as $haveAction){
            $key = $haveAction['lesson_id'].'_'.$haveAction['mode'].'_'.$haveAction['action_id'].'_'.$haveAction['sort'];
            $haveActionDicts[$key] = $haveAction;
         }

        foreach($action_ids_post as $k=>$action_id){
            $data['lesson_id'] = $lesson_id;
            $data['mode'] = $mode;
            $data['action_id'] = $action_id;
            $data['sort'] = $k;
            $subKey = $lesson_id.'_'.$mode.'_'.$action_id.'_'.$k;
            $submitActionDicts[$subKey] = $data;

            $action_ids[] = $data;
        }

        //库里有，外面没有的，删除

        $deteleDatas = array_diff_key($haveActionDicts, $submitActionDicts);//删除


        foreach($deteleDatas as $deleData){
            $lesson_id = $deleData['lesson_id'];
            $mode = $deleData['mode'];
            $action_id = $deleData['action_id'];
            $this->selfTable()->delete(array('lesson_id'=>$lesson_id,'mode'=>$mode,'action_id'=>$action_id));

        }

        //外面有，库里fds没有，增加dss
        $insertDatas = array_diff_key($submitActionDicts, $haveActionDicts);//增加爱


        foreach($insertDatas as $insertData){
            $this->selfTable()->add($insertData);
        }

    }



}
