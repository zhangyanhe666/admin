<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Model;

use Application\Model\SysModel;
use Library\Application\Common;
class Cantv extends SysModel{
    
    public $defaultDb   =   'wukong';
    public function init() {}
    
    public function setDb($db){
        $this->defaultDb    =   $db;
        return $this;
    }
    public function getDefaultDb(){
        return $this->defaultDb;
    }

    //导入父分类信息
 /*   public function importVideoParentCategory(){
        $types  =   $this->getServer('Model\CantvHttp')->videoParentCategory();
        $ids    =   array_column($types,'id');
        $names  =   array_column($types,'name');
        $this->getServer($this->getDefaultDb().'.can_category')->batchInsert1(array('id','name'),$ids,$names);
    }*/
    //导入子分类信息
    public function importVideoCategory(){
        //获取父分类信息
        $msg    =   array();
        $types  =   $this->getServer('Model\CantvHttp')->videoParentCategory();
      //  var_dump($types);exit;
        foreach ($types as $type){
            try {                
                $categorys  =   $this->getServer('Model\CantvHttp')->videoCategory($type['id']);
                $ids        =   array_column($categorys,'id');
                $names      =   array_column($categorys,'name');
                $this->getServer($this->getDefaultDb().'.can_category')->batchInsert1(array('id','name','parent_id'),$ids,$names,$type['id']);
            } catch (\Exception $ex) {
                $msg[]  =   $ex->getMessage();
            }
        }
        return $msg;
    }
    
    public function importVideo(){
        //获取父分类信息
        $pageSize   =   100;
        $msg    =   array();
        $types  =   $this->getVideoCategory(FALSE);
        $insertColumns  =   array('id');
        $updateColumns  =   array('name','image','currentNum','score','createDate','is_fee','typeID');
        $canVideo       =   $this->getServer($this->getDefaultDb().'.can_video')->batchUpdate($updateColumns);
        $insertColumns  =   array_merge($insertColumns,$updateColumns);
        foreach ($types as $type){
            $pageNumber =   1;
            while ($videos     =   $this->getServer('Model\CantvHttp')->videoByCategory($type['id'],$type['parent_id'],$pageNumber,$pageSize)){
                $list       =   array();
                $category   =   array();
                foreach ($videos as $v){
                    if(!empty($v['name']) && !empty($v['image']) && !empty($v['typeID'])){
                        $vv     =   $this->getDate($v, $insertColumns);
                        array_pop($vv);
                        $vv[]   =   $type['id'];
                        $list[] =   $vv;
                    }
                }
                $canVideo->batchInsert($insertColumns,$list);                
                $pageNumber++;
            }
        }
        return $msg;
    }

    public function importVideoInfo(){
        $pageNumber =   1;
        $pageSize   =   10;
        $insertColumns  =   array('id');
        $updateColumns  =   array('classname','zone','director','actor','language','releaseDate','information','playcount','showtype');
        $canVideo       =   $this->getServer($this->getDefaultDb().'.can_video')->batchUpdate($updateColumns);
        $insertColumns  =   array_merge($insertColumns,$updateColumns);
        while ($videos     =   $this->getVideo($pageNumber,$pageSize)){
            $list       =   array();
            foreach ($videos as $v){
                $video      =   $this->getServer('Model\CantvHttp')->videoInfo($v['id']);
                if(!empty($video)){
                    $list[]     =   $this->getDate($video, $insertColumns);
                }
            }
            if(!empty($list)){
                $canVideo->batchInsert($insertColumns,$list);
            }
            $pageNumber++;
        }
    }
    
    public function importLive(){
        $lives  =   $this->getServer('Model\CantvHttp')->liveList();
        $insertColumns  =   array('channelId','name','playurl','icon');
        $apiColumns     =   array('channelId','channelName','m3u8Url','logo');
        $liveService    =   $this->getServer($this->defaultDb.'.can_live');
        foreach ($lives as $v){
            $list[]     =   $this->getDate($v, $apiColumns);
        }
        $liveService->batchInsert($insertColumns,$list);     
    }

    public function getDate($v,$columns){
        $tmp    =   array();
        foreach ($columns as $column){
            $tmp[]  =   $v[$column];
        }
        return $tmp;
    }
    public function getVideo($page,$pageNum=100){
        $start  =   $page * $pageNum;
        return $this->getServer($this->getDefaultDb().'.can_video')->offset($start)->limit($pageNum)->getAll()->toArray();
    }

    //获取影视分类
    public function getVideoCategory($parentCategory=true){
        $where  =   array();
        if($parentCategory){
            $where['parent_id']    =   0;
        }else{
            $where[]    =   'parent_id != 0';
        }
        $types  =   $this->getServer($this->getDefaultDb().'.can_category')->where($where)->getAll()->toArray();
        return $types;
    }
    
}