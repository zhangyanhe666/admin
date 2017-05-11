<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Cache;

class Memcache{
   // public $serviceKey = 'serviceKey';
    public $service;
    public $cached;
    public $cacheMap;
    public $cacheService;
    public $mem;
    public $monitor;
    public $disable =   false;
    public function __construct($service) {
        $this->service  =   $service;
        $this->cached   =   $this->service->get('config')->memcache['cached'];
        $this->cacheMap =   $this->service->get('config')->memcache['cacheMap'];
        if($this->cached && is_array($this->cacheMap)){
            list($this->cacheService)   =   each($this->cacheMap);
        }
    }
    public function setMonitor($monitor){
        $this->monitor  =   $monitor;
        return $this;
    }
    public function setMemService($memService){
        $this->cacheService     =   $memService;
        $this->mem              =   null;
        return $this;
    }
    public function getMem(){
        if(empty($this->mem) && $this->cached){
            if(!isset($this->cacheMap[$this->cacheService]) || !isset($this->cacheMap[$this->cacheService]['host']) || !isset($this->cacheMap[$this->cacheService]['port'])){
                throw new \Exception('memcache['.$this->cacheService.']设置错误');
            }
            $this->mem  =   new \Memcache();
            @$this->mem->connect($this->cacheMap[$this->cacheService]['host'],$this->cacheMap[$this->cacheService]['port'] , 10);
        }
        return $this->mem;
    }
    public function triggerMonitor($key,$monitor){
        $monitor    =   \Library\Tool\ToolArray::merge(array($this->monitor), $monitor);
        if(!empty($monitor)){
            foreach ($monitor as $m){
                $data   =   $this->getMem()->get($m);
                $data[] =   $key;
                $this->getMem()->set($m,$data);
            }
        }
        return $this;
    }
    public function set($key,$val,$time=0,$monitor=array()) {
        if(empty($this->getMem())){
            return false;
        }
        $this->triggerMonitor($key,$monitor);
        return $this->getMem()->set($key,$val,0,$time);
    }
    public function get($key){  
        if(empty($this->getMem())){
            return false;
        }
        return $this->getMem()->get($key);
    }
    public function deleteMonitor() {   
        if(empty($this->getMem())){
            return $this;
        }
        if(!empty($this->monitor) && ($data   =   $this->getMem()->get($this->monitor))){
            foreach ($data as $v){
                $this->getMem()->delete($v);
            }
            $this->getMem()->delete($this->monitor);
        }
        return $this;
    }
    public function flush(){
        if(empty($this->getMem())){
            return false;
        }
        return $this->getMem()->flush();
    }
}
