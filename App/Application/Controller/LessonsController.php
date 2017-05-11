<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Library\Application\Common;
use Application\Base\PublicController;
use Library\Db\Sql\Predicate\In;
use Application\Tool\Html;
class LessonsController extends PublicController{

    public function indexAction() {
        //模板赋值
        parent::indexAction();
        Html::addOption('', '计算时长');
        //Html::addOption('checkLesson', '检测课程');
        $this->viewData()->addTpl('dailyup/lessonList');
       }

    public function checkLessonAction(){
        $id = $this->getRequest()->getQuery('id');
        $data =  $this->selfTable()->where(array('id'=>$id))->getRow()->toArray();
        $lesson_id = $data['lesson_id'];

        $url = 'http://dailyup.wukongtv.com/lesson/video?lesson_id=%s&mode=%s';
        $t_url = sprintf($url,$lesson_id,1);//训练模式
        $l_url = sprintf($url,$lesson_id,2);//学习模式

        $datas = $this->curl($t_url);
        $datas = json_decode($datas,TRUE);
        $msg = $this->_checkLesson($datas,1);
        if(empty($msg)){
            $datas = $this->curl($l_url);
            $datas = json_decode($datas,TRUE);
            $msg = $this->_checkLesson($datas,2);
        }

        if(!empty($msg)){
            return $this->responseError($msg);
        }
    }

    private function _checkLesson($datas,$mode){
        $msg = '';

        $items = $datas['data']['items'];
        $count = count($items);

        foreach($items as $k =>$item){
            if($k==$count-1){
                break;
            }

            if(array_key_exists('video',$item)&&array_key_exists('video',$items[$k+1])){
                if($mode==1){
                    $msg = '训练模式课程错误：第%s个动作和第%s动作都是视频';
                }else{
                    $msg = '学习模式课程错误：第%s个动作和第%s动作都是视频';
                }

                $msg = sprintf($msg,$k+1,$k+2);
                return $msg;
            }

            if(array_key_exists('video',$item)){
                $duration = 0;
                $item['duration'];
                if(array_key_exists('widgets',$item)){
                    $widgets = $item['widgets'];
                    foreach($widgets as $v){
                        $duration = $duration + $v['duration'];
                    }
                    if($item['duration']<$duration){
                        $action_id = $item['action_id'];
                        $msg = '学习模式课程错误：动作id:%s 持续时间不正确';
                        $msg = sprintf($msg,$action_id);
                        return $msg;
                    }

                }
            }
        }
        return $msg;
    }

    public function durationAction(){
        //需要计算各种模式的id
        $id = $_GET['id'];
        $data =  $this->selfTable()->where(array('id'=>$id))->getRow()->toArray();
        $lesson_id = $data['lesson_id'];

        $t_mode = 1;//训练
        $l_mode =2;

        $res['t_mode'] = $t_due = $this->duration($lesson_id,$t_mode);//教学模式的问题
        $res['l_mode'] = $l_due = $this->duration($lesson_id,$l_mode);
        $res['calorie'] = $calorie = $this->cals($lesson_id,$l_mode);

        $this->_teach_subtitle($lesson_id);
        $this->selfTable()->update(array('learn_duation'=>$l_due,'training_duation'=>$t_due,'calorie'=>$calorie),array('id'=>$id));

        //return $res;
        echo json_encode($res);
        exit();
    }

    //教学模式下，各个动作
    private function _teach_subtitle($lesson_id){
        $url = 'http://dailyup.wukongtv.com/api/lesson/video?lesson_id='.$lesson_id.'&mode=1';
        $datas    =   json_decode($this->curl($url),TRUE);
        $items = $datas['data']['items'];

        foreach($items as $k=>$item){
            if(array_key_exists('video',$item)){
                $sub_title = $items[$k-1]['sub_title'];
                $action_id = $item['te_action_id'];
                $mode=3;
                $this->selfModel('lesson_action')->update(array('subtitle'=>$sub_title),
                    array('lesson_id'=>$lesson_id,'action_id'=>$action_id,'mode'=>$mode,));
            }

        }
    }


    public function LessonAction($lesson_id,$mode){

        $where['lesson_action.lesson_id'] = $lesson_id;
        $where['lesson_action.mode'] = $mode;


        $res = $this->model->leftjoin('lesson_action', 'lesson_action.action_id', '=', 'actions.action_id')->select('actions.action_id', 'actions.action_title','actions.calorie','actions.duration',
            'actions.tips','actions.image')->orderBy('lesson_action.sort', 'asc')->where($where)->get()->toArray();
        return $res;
    }



    private function duration($lesson_id,$mode){
        $lesson_duration = 0;
        //课程中获取课程动作获取动作元素表中的信息
        $actions   =   $this->selfModel('lesson_action')->columns(array('*'))
            ->join('action_element',"lesson_action.action_id = action_element.action_id",array('*'))
            ->where("lesson_action.lesson_id= $lesson_id  and lesson_action.mode = $mode" )
            ->getAll()->toArray();

        //所有的动作元素

        foreach($actions as $v){
            $element_ids[]=$v['element_id'];
        }

        $eles = $this->selfModel('elements')->where(array(new In('id',$element_ids)))->getAll()->toArray();

        foreach($eles as $ele){
            $elesDict[$ele['id']] = $ele;
        }
        foreach($actions as $v){

            switch($elesDict[$v['element_id']]['type']){
                case 'video':
                case 'rest':
                case 'gap':
                case 'transition':
                    $lesson_duration = intval($v['duration'])+$lesson_duration;
                    break;
            }

        }

        return $lesson_duration;
    }

    private function cals($lesson_id,$mode){
        $lesson_cal = 0;
        //卡路里
        $actions_cals   =   $this->selfModel('lesson_action')
            ->join('actions',"lesson_action.action_id = actions.action_id",array('calorie'))
            ->where("lesson_action.lesson_id= $lesson_id  and lesson_action.mode = $mode" )
            ->getAll()->toArray();

        foreach($actions_cals as $k =>$action){
            $lesson_cal = intval($action['calorie'])+$lesson_cal;
        }
        return $lesson_cal;
    }


    public function   checkLesson(){
        $lesson_id = $_GET['lesson_id'];
        $url = 'http://api.dailyup.com/api/lesson/video?lesson_id='.$lesson_id.'&mode=1';
        $datas = json_decode($this->curl($url));


        //获取数据
        //循环动作，检测video，如果是video，讲动作id拿出来，而后计算前一个的动作中的subtitle,
    }


    public function curl($url){
        $xml    =   $this->getServer('curl')
            ->setopt(CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)')
            ->setopt(CURLOPT_PROXYTYPE, CURLPROXY_HTTP)
            ->setopt(CURLOPT_PROXYAUTH, CURLAUTH_BASIC)
            //->setopt(CURLOPT_PROXY,'124.88.67.20')
            ->setopt(CURLOPT_PROXYPORT,80)
            ->setopt(CURLOPT_AUTOREFERER,1)
            ->setopt( CURLOPT_ENCODING,'gzip')
            ->exec($url)->result();
        return $xml;
    }
}
