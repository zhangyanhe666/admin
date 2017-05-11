<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Library\ServiceManager;
use Library\Application\Config;
class ServiceManager{
    public $instanceClasses =   array();
    public $factorices      =   array();    
    public $instanceService =   array();
    public $instances       =   array();
    public $customService;
    public function __construct(ServiceManagerConfig $ServiceManagerConfig) {
        $ServiceManagerConfig->configureServiceManager($this);
    }
    //可以增加工厂规则
    public function get($name,$useAlreadyExists =true){
        if(isset($this->instances[$name]) && $useAlreadyExists){
            return $this->instances[$name];
        }
        $instance   =   null;
        if(isset($this->factorices[$name])){
            $instance   =   $this->createFromFactory($name);
        }
        if($instance == null && isset($this->instanceClasses[$name])){
            $instance   =   $this->createFromInstance($name);
        }
        if($instance == null && isset($this->instanceService[$name])){
            $instance   =   $this->createFromInstanceService($name);
        }
        if($instance == null && is_callable($this->customService)){
             $instance  =   call_user_func($this->customService,$name);
        }
        if($instance == null){
            throw new \Exception($name.' not find');
        }
        $this->instances[$name] =   $instance;
        return $instance;
    }
    public function setCustomService($serviceCallable){
        $this->customService    =   $serviceCallable;
    }
    public function setServer($name,$server){
        $this->instances[$name] =   $server;
        return $this;
    }
    public function createFromInstance($name){
        $instance   = $this->instanceClasses[$name];
        if(!class_exists($instance)){
            throw new \Exception('create "'.$instance.'" Failure');
        }
        return new $instance;
    }
    public function createFromFactory($name){
        $factory    =   $this->factorices[$name];
        if(is_string($factory) && class_exists($factory)){
            $factory    =   new $factory;
            $this->factorices[$name]    =   $factory;
        }
        if($factory instanceof Factory\FactoryInterface){
            $instance   =   $factory->createService($this);
        }elseif(is_callable($factory)){
            $instance   =   call_user_func($factory,$this,$name);
        }else{
            throw new \Exception('create "'.$name.'" Failure');
        }
        return $instance;
    }
    public function createFromInstanceService($name){
        $instance   = $this->instanceService[$name];
        if(!class_exists($instance)){
            throw new \Exception('create "'.$instance.'" Failure');
        }
        return new $instance($this);
    }
    public function setFactories($name,$factory=''){
        if(is_array($name)){
            foreach($name as $k=>$v){
                $this->factorices[$k]   =   $v;
            }
        }elseif($factory != ''){
            $this->factorices[$name]   =   $factory;
        }
    }
    public function setInstanceClasses($name,$instance=''){
        if(is_array($name)){
            foreach($name as $k=>$v){
                $this->instanceClasses[$k]   =   $v;
            }
        }elseif($instance != ''){
            $this->instanceClasses[$name]   =   $instance;
        }
    }
    public function setInstanceService($name,$instance=''){
        if(is_array($name)){
            foreach($name as $k=>$v){
                $this->instanceService[$k]   =   $v;
            }
        }elseif($instance != ''){
            $this->instanceService[$name]   =   $instance;
        }
    }
}