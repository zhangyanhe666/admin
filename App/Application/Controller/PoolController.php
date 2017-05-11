<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
use Application\Tool\Html;
class PoolController extends PublicController{
    /*
     * #已生效
SELECT * FROM (SELECT * FROM pick_pool WHERE start_time	<NOW() ORDER BY start_time DESC) AS table1 GROUP BY sort 
     * #未生效
SELECT * FROM pick_pool WHERE start_time>NOW() 
     * #已过期
SELECT * FROM pick_pool WHERE start_time <NOW() AND id NOT IN(SELECT id FROM (SELECT id FROM (SELECT * FROM pick_pool WHERE start_time<NOW() ORDER BY start_time DESC) AS table1 GROUP BY sort) AS bb)
     */

    public function allAction(){
        $this->selfTable()->order($this->order());
        parent::indexAction();
        $tool   =   array(
            'index'=>'已生效',
            'future'=>'未生效',
            'old'=>'已过期',
            'all'=>'全部',
        );
        $current    =   $this->router()->getAction();
        foreach ($tool as $k=>$v){
            if($current == $k){
                Html::addTool($k, $v,array(),'');
            }else{
                Html::addTool($k, $v);
            }
        }        
    }
    public function indexAction(){
        $sort       =   '';
        $wktype     =   '';
        $data       =   $this->selfTable()->order($this->order())->where(array("start_time<NOW()",'black'=>0))->getAll()->toArray();
        foreach ($data as $k=>$v){
            if($v['sort']==$sort && $v['wktype']=$wktype){
                unset($data[$k]);
            }
           $sort    =   $v['sort'];
           $wktype  =   $v['wktype'];
        }
        $this->selfTable()->where(array($this->selfTable()->table.'.id'=>array_column($data,'id')));
        $this->allAction();
    }
    public function futureAction(){
        $this->selfTable()->where(array("start_time>NOW()"));
        $this->allAction();
    }
    public function oldAction(){
        $sort       =   '';
        $wktype     =   '';
        $oldid      =   array(0);
        $data       =   $this->selfTable()->order($this->order())->where(array("start_time<NOW()",'black'=>0))->getAll()->toArray();
        foreach ($data as $k=>$v){
            if($v['sort']==$sort  && $v['wktype']=$wktype ){
                $oldid[]    =   $v['id'];
            }
           $sort    =   $v['sort'];
           $wktype  =   $v['wktype'];
        }
        $this->selfTable()->where(array($this->selfTable()->table.'.id'=>$oldid));
        $this->allAction();
    }
    private function order(){
        return array($this->selfTable()->table.'.wktype',$this->selfTable()->table.'.sort',$this->selfTable()->table.'.start_time DESC');
    }

}