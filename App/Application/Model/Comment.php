<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Application\Model\SysModel;
class Comment extends SysModel{
    
    public function init() {
        $this->setTable('video_comment');
        parent::init();
    }
    public function getList($id,$limit=3000){
        return $this->where(['switch' => 0, "id>{$id}",'create_time>DATE_ADD(NOW(),INTERVAL -7 DAY)'])->limit($limit)->getAll()->toArray();
    }
    public function filter($content,$callback,$id=0){
        $data   =   $this->getList($id);
        if(!empty($data)){
            $filterData     =   array_filter($data,$callback);
            $ids            =   array_column($filterData,'id');
            if(!empty($ids)){
                $this->update(['switch'=>1,'remarks'=>$content],['id'=>$ids]);
            }
            $endData    =   end($data);
            $this->filter($content,$callback,$endData['id']);
            
        }
        return TRUE;
    }
}
