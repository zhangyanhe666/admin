<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Application;
class Controller{
    
    /**
     * [$serviceManager 服务管理器]
     * @var [type]
     */
    private $serviceManager;

    /**
     * 初始化控制器
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @param    [type]     $serviceManager [description]
     */
    public function __construct($serviceManager){
        $this->serviceManager    =   $serviceManager;
    }
    //找不到页面处理
    protected function notFound(){
        $this->router()->error('请求不存在');
    }

    /**
     * 控制器调度
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    public function onDispatch(){
        $method    =   $this->router()->getAction().'Action';
        if (!method_exists($this, $method)) {
            $method = 'notFound';
        }
        $response    = call_user_func(array($this,$method));
        return $response;
    }
    //获取request对象
    protected function getRequest(){
        return $this->getService('request');
    }

    /**
     * 读取配置信息
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    protected function config(){
        return $this->getService('config');
    }

    /**
     * 路由对象
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    protected function router(){
        return $this->getService('router');
    }
    
    /**
     *获取server
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    protected function getserviceManager(){
        return $this->serviceManager;
    }

    /**
     * 获取服务类
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @param    [type]     $server           [description]
     * @param    boolean    $useAlreadyExists [description]
     * @return   [type]                       [description]
     */
    protected function getService($server,$useAlreadyExists=true){
        return $this->getserviceManager()->get($server,$useAlreadyExists);
    }


}
