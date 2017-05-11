<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Library\Application\Common;
use Application\Base\PublicController;
class ActionElementController extends PublicController{


    public function doAddAction(){

       $addDatas = $this->tplFormat()->doAdd();
       $checkRes = $this->checkAudio($addDatas);
        if($checkRes['code']==-1){
            return $this->responseError($checkRes['msg']);
        }else{
            $this->selfTable()->add($addDatas);
        }
    }

    //保存单个列编辑信息
    public function doEditColumnAction(){
        //从库中取出数据,将修改的提交过来
        $id = $this->getRequest()->getPost('id');
        $data =  $this->selfTable()->where(array('id'=>$id))->getRow()->toArray();
        $element_id = $data['element_id'];
        $eData = $this->selfModel('elements')->where(array('id'=>$element_id))->getRow()->toArray();


        $editData = $this->tplFormat()->doAdd();
        foreach($editData as $k=>$v){
            $data[$k]=$v;
        }

        if($eData['type']=='audio'){
            $checkRes = $this->checkAudio($data);
            if($checkRes['code']==-1){
                return $this->responseError($checkRes['msg']);
            }else{
                $this->selfTable()->edit($this->getRequest()->getPost('id'),$editData);
            }
        }else{
            $this->selfTable()->edit($this->getRequest()->getPost('id'),$editData);
        }

    }

    //处理音频时长重复
    private function checkAudio($addDatas){
        $res['code']=0;
        $res['msg']='';

        $action_id = $addDatas['action_id'];
        $startAtAdd = intval($addDatas['start_at']);
        $duration = intval($addDatas['duration']);
        $id = empty($addDatas['id'])?0:$addDatas['id'];//编辑的时候传过来的是一直的，所以要不用比较

        $actionElements = $this->selfTable()->columns(array('start_at','duration'))
            ->join(array('e'=>'elements'),"action_element.element_id = e.id",array(),'left')
            ->where("action_element.action_id = $action_id and action_element.id!=$id and e.type='audio'")
            ->order(array('start_at desc'))
            ->getAll()
            ->toArray();

        $count = count($actionElements);

        if($count>0) {
            foreach ($actionElements as $k => $v) {
                $startAtHave = intval($v['start_at']);
                if ($startAtHave == $startAtAdd) {
                    $res['msg'] = '动作元素开始时间重复';
                    $res['code']=-1;
                    return $res;//一个动作中一个id,唯一键，这个会报错
                }
                if ($startAtAdd >= $startAtHave) {
                    break;
                }
            }

            $currentElement = $addDatas;


            if ($k-1 >= 0){
                $nextElement = $actionElements[$k-1];
                if (intval($currentElement['start_at']) + intval($currentElement['duration'])>intval($nextElement['start_at'])){
                    $msg = '与下一个时间重合,下一个开始时间:%s,持续时间:%s';

                    $msg = sprintf($msg, $nextElement['start_at'], $nextElement['duration']);
                    $res['code']=-1;
                    $res['msg']=$msg;
                    return $res;
                }
            }elseif ($k>= 0) {
                $lastElement = $actionElements[$k];


                if (intval($lastElement['start_at']) + intval($lastElement['duration'])>intval($currentElement['start_at'])) {

                    $msg = '与上一个时间重合,上一个开始时间:%s,持续时间:%s';
                    $msg = sprintf($msg, $lastElement['start_at'], $lastElement['duration']);
                    $res['code']=-1;
                    $res['msg']=$msg;
                    return $res;
                }
            }


        }

        $actionVideos = $this->selfTable()
            ->join(array('e'=>'elements'),"action_element.element_id = e.id",array(),'left')
            ->where("action_element.action_id = $action_id and action_element.id!=$id and e.type='video'")
            ->order(array('start_at desc'))
            ->getAll()
            ->toArray();
        if (count($actionVideos)>1){
            $msg = '动作中视频不唯一';
            $res['code']=-1;
            $res['msg']=$msg;
            return $res;
        }


        if((count($actionVideos)==1)&&($startAtAdd+$duration>$actionVideos[0]['duration'])){
            $msg = '音频超出视频总长度';
            $res['code']=-1;
            $res['msg']=$msg;
            return $res;
        }

        return $res;

    }

}




//在编辑的时候，