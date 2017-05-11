<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
use Application\Tool\Html;
class ActivityController extends PublicController
{
    public function indexAction() {
        parent::indexAction();
        Html::delOption();
    }

    public function doEditColumnAction() {
        if($this->getRequest()->getPost('switch')==1){
            return $this->responseError('不能关闭活动,请联系管理员');
        }else{
            $item   =   $this->selfTable()->getItem($this->getRequest()->getPost('id'));
            $time   =   strtotime($item->end_time)   -   strtotime($item->start_time);
            $total  =   $this->selfModel('prize')->columns(['total'=>new \Library\Db\Sql\Predicate\Expression('sum(total)')])
                             ->where(array('act_id'=>$item->id))->getRow()->total;
            
            if($total > 0){

                if($time < $total){
                    return $this->responseError('活动时间过短');
                }
                $aver   =   $time/$total;
                $winTime=   array();
                for($i=0;$i<=$total+1;$i++){
                    if($i%2 == 0){                    
                        $rand   =   rand(0, $aver*2);
                    }else{
                        $rand   =   $aver*2-$rand;
                    }
                    $winTime[]  =   date('Y-m-d H:i:s',strtotime($item->start_time)+($aver*$i)+$rand);
                }
                $winTime        =   array_slice($winTime, 0,$total);
                $act_id         =   array_fill(0,$total,$item->id);
                $this->selfModel('activity_win_time')->delete(array('act_id'=>$item->id));
                $this->selfModel('activity_win_time')->batchInsert(array('act_id','winTime'),  array_map(NULL, $act_id,$winTime));
            }
        }
        return parent::doEditColumnAction();
    }
        //添加
    public function doAddAction(){
        if(strtotime($this->getRequest()->getPost('start_time')) > strtotime($this->getRequest()->getPost('end_time'))){
            return $this->responseError('结束时间要大于开始时间');
        }
        return parent::doAddAction();
    }
}