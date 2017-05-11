<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Application;

class Router{
    public $control;
    public $action;
    public $controlName;
    public $service;
    public $route;
    public function __construct($serviceManager) {
        $this->service  =   $serviceManager; 
        $uri            =   trim($this->getServer('request')->getUri(),'/');
        $project        =   $this->getServer('config')->project;
        $uri            =   strpos(strtolower($uri), strtolower($project)) === 0 ? substr($uri,strlen($project)+1) : $uri;
        list($this->control,$this->action)          =   array_filter(explode('/', $uri)) + array($this->getServer('config')->router['control'],$this->getServer('config')->router['action']);
    }
    public function getServer($serviceName){
        return $this->service->get($serviceName);
    }
    public function getControl(){
        return $this->control;
    }
    public function getAction(){
        return $this->action;
    }
    //生成uri
    public function buildUri($uri=array()){
        if(!is_array($uri)){
            return  $uri;
        }        
        $project    =   isset($uri['project']) ? $uri['project'] : '';
        $control    =   !isset($uri['control']) ? $this->control : $uri['control'];
        $action     =   !isset($uri['action']) ? $this->getAction() : $uri['action'];
        return implode('/', array_filter(array($project,$control,$action)));
    }
    public function error($msg){
        if(!$this->getServer('config')->production){
            echo $msg;exit;
        }
        $this->toUrl($this->getServer('config')->error,array('msg'=>$msg));
    }
    //生成url
    public function url($uri=array(),$param=array(),$useQuery=false){
        $url    =   $this->getServer('request')->host.'/'.trim($this->buildUri($uri),'/');
        if(!is_array($param)){
            throw new \Exception('param需要是数组类型');
        }
        $query  =   $useQuery   ?   $this->getServer('request')->queryString($param) :   http_build_query($param);
        return  !empty($query) ? $url.'?'.$query : $url;
    }
    public function referer($uri=array()){
        $uri        =   empty($uri) ? array('action'=>'index'): $uri;
        $referer    =   $this->getServer('request')->getQuery('url',getenv('HTTP_REFERER'));
        empty($referer) && ($referer = $this->url($uri));
        return $referer;
    }
    public function toUrl($uri,$param=array()){
        if(is_array($uri)){
            $url    =   $this->url($uri,$param);
        }  else {
            $url    =   $uri;
        }
        header("location:{$url}");
        exit;
    }
}
