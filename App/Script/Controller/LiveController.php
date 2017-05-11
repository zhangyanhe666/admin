<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Controller;

class LiveController extends BaseController{
    
    public function indexAction() {
        $data   =   file_get_contents($this->getServer('config')->filePath('Cache/Tmp/itv_json_v6.php'));
        $data   =   json_decode($data,TRUE);
        $ids    =   array_column($data['live'],'id');
        $name    =   array_map(function($v){
            return str_replace('高清', '', $v);
        },array_column($data['live'],'name'));
        $quality    =   array_column($data['live'],'quality');
        
        $this->getServer('wukong.zhibo_hdp')->batchInsert1(array('id','name','quality'),$ids,$name,$quality);
        
    }
    
    public function channelAction(){
        $data   =   $this->getServer('Model\LiveHttp')->dsj();
        print_r($data);exit;
    }
}