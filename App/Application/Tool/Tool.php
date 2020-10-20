<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/****
 * 数据表结构处理类
 */


namespace Application\Tool;
class Tool{
    private $__service;
    public function init(){
        
    }
    public function getService($server,$useAlreadyExists=true){
        return $this->getServiceManager()->get($server,$useAlreadyExists);
    }
    public function getServiceManager(){
        return $this->__service;
    }
    //设置Service
    public function setServiceManager($server){
        $this->__service  =   $server;
        $this->init();
        return $this;
    }
}