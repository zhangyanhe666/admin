<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Model;
use Application\Model\SysModel;
use Library\Db\Sql\Expression;
class Video extends SysModel{
    public $db  =   'wukong214';
    
    public function init(){}
    public function setDb($db){
        $this->db   =  $db;
        return $this;
    }
    public function getVideo($num=100){
        $wkid   =   intval($this->getServer($this->db.'.screen_map')->columns(['max'=>  new Expression('max(wkid)')])->getRow()->max);
        $res    =   $this->getServer($this->db.'.v_all')->columns(['wkid','wktype','tag','showtime','area'])->where(['wkid > '.$wkid])->limit($num)->order('wkid')->getAll()->toArray();
        return $res;
    }
    public function getScreen(){
        $data   =   array();
        $res    =   $this->getServer($this->db.'.v_screening')->getAll()->toArray();
        foreach ($res as $v){
            $data[$v['wktype']][$v['s_type']][$v['s_name']] =   $v['s_nameid'];
        }
        return $data;
    }
    public function addScreen($tagids,$wkids){        
        $res    =   $this->getServer($this->db.'.screen_map')->batchInsert(['tag_id','wkid'],array_map(null,$tagids,$wkids));
    }
}