<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Application;
class Controller{
    
    private $serverManager;
    //通过配置文件传入需要的信息
    public function init(){}
    //找不到页面处理
    protected function notFound(){
        $this->router()->error('请求不存在');
    }
    //index默认使用方法
    public function indexAction(){
        return $this->defaultResponse();
    }
    public function onDispatch(){
        $method    =   $this->router()->getAction().'Action';
        if (!method_exists($this, $method)) {
            $method = 'notFound';
        }
        $response    = call_user_func(array($this,$method));
        Common::setTimeAnchor('end');
        return is_object($response) ? $response : $this->defaultResponse();
    }
    public function setServerManager($serverManager){
        $this->serverManager    =   $serverManager;
        return $this;
    }
    //获取request对象
    protected function getRequest(){
        return $this->getServer('request');
    }
    protected function config(){
        return $this->getServer('config');
    }
    protected function json(){
        return $this->getServer('jsonresponse');
    }
    //路由
    protected function router(){
        return $this->getServer('router');
    }
    //获取server
    protected function getServerManager(){
        return $this->serverManager;
    }
    protected function viewData(){
        return $this->getServer('responseData');
    }
    protected function defaultResponse(){
        return $this->json();
    }
    //检测请求类别
    protected function isAjax(){
        return $this->getRequest()->isAjax();
    }

    protected function getServer($server,$useAlreadyExists=true){
        return $this->getServerManager()->get($server,$useAlreadyExists);
    }
    /**
     * tmp处理
     * @param string $tpl
     * @return template;
     */
    protected function template($tpl=''){
        //设置默认模板
        if(empty($tpl)){
            $control    =   strtolower($this->router()->getControl());
            $tpl        =   "{$control}/{$this->router()->getAction()}";
        }
        $this->viewData()->setTpl($tpl);
        return $this->getServer('template');
    }

}
