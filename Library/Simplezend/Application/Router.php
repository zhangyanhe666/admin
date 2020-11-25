<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Application;
/**
 * 路由类
 */
class Router{
    /**
     * [$control 控制器名]
     * @var [string]
     */
    public $control;
    /**
     * [$action 方法名]
     * @var [string]
     */
    public $action;

    /**
     * [$service 服务管理者]
     * @var [serviceManager]
     */
    public $service;

    /**
     * [$query 路由查询条件]
     * @var array
     */
    public $query=[];
    /**
     * [$requestQuery 请求的查询条件]
     * @var [type]
     */
    public $requestQuery;

    /**
     * router类初始化
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @param    [type]     $serviceManager [description]
     */
    public function __construct($serviceManager) {
        $this->service  =   $serviceManager; 
        $this->requestQuery    =   $this->getService('request')->getQuery()->toArray();
        $this->initRouter();
    }

    /**
     * 初始化路由
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    public function initRouter(){
        $router     =   array_values(
                                array_filter(
                                    explode('/', $this->getService('request')->getUri())
                                ));
        list($this->control,$this->action)  =   $router + $this->getService('config')->router->toArray();
    }

    /**
     * 获取服务对象
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @param    [type]     $serviceName [description]
     * @return   [type]                  [description]
     */
    public function getService($serviceName){
        return $this->service->get($serviceName);
    }

    /**
     * 获取control
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    public function getControl(){
        return $this->control;
    }

    /**
     * 获取action
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    public function getAction(){
        return $this->action;
    }

    /**
     * 获取路由uri
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    public function getUri(){
        $uri    =   '/'.$this->getControl().'/'.$this->getAction();
        $this->initRouter();
        return $uri;
    }

    /**
     * 获取路由url
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    public function getUrl(){
        return $this->getService('request')->host.$this->getUri();
    }

    /**
     * 设置路由控制器
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @param    [type]     $control [description]
     */
    public function setControl($control){
        $this->control  =   $control;
        return $this;
    }

    /**
     * 设置路由方法名
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @param    [type]     $action [description]
     */
    public function setAction($action){
        $this->action   =   $action;
        return $this;
    }

    public function error($msg){
        if(!$this->getService('config')->production){
            echo $msg;exit;
        }
        $this->toUrl($this->getService('config')->error,array('msg'=>$msg));
    }

    /**
     * 设置查询条件
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @param    [type]     $query [查询参数]
     * @param    boolean    $cover [是否覆盖]
     */
    public function setQuery($query,$cover=false){
        $this->query  =   $cover ? array_merge($this->requestQuery,$query) :    $query;
        return $this;
    }

    /**
     * 获取路由url
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    public function url(){
        $url    =   $this->getUrl();
        if(!empty($this->query)){
            $url    .=   '?'.http_build_query($this->query);
        }
        return $url;
    }

    public function referer($uri=array()){
        $uri        =   empty($uri) ? array('action'=>'index'): $uri;
        $referer    =   $this->getService('request')->getQuery('url',getenv('HTTP_REFERER'));
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
