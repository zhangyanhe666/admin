<?php

/* 
 * 框架入口文件
 * 处理配置文件加载和必要插件的启动
 */
namespace Library\Application;
use Library\ServiceManager\ServiceManager;
use Library\ServiceManager\ServiceManagerConfig;
use Library\Application\Common;
class Application{
    /**
     * 入口文件类,需要做如下事情
     * 1.加载初始化配置
     * 2.执行启动程序
     */
    public $serverManager;
    //初始化App
    public function __construct($serverManager) {
        $this->serverManager    =   $serverManager;
    }
    public static function init($config){
        $serverManager    =   new ServiceManager(new ServiceManagerConfig($config));
        return $serverManager->get('Application');
    }
    //执行程序
    public  function run(){
        try {
            //设置是否报错
            $this->getServer('error')->setPhpError($this->getServer('config')->production);
            Common::setTimeAnchor('start');
            $control    =   $this->serverManager->get('controller');
            $control->init();
            $control->onDispatch()->result();
        } catch (\Exception $exc) {
            $this->serverManager->get('exceptionhandle')->printMsg($exc);
        }   
    }
    public function getServer($serverName){
        return $this->serverManager->get($serverName);
    }
}