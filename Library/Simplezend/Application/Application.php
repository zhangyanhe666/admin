<?php

/* 
 * 框架入口文件
 * 处理配置文件加载和必要插件的启动
 */
namespace Library\Application;
use Library\ServiceManager\ServiceManager;
use Library\ServiceManager\ServiceManagerConfig;
class Application{
    /**
     * 入口文件类,需要做如下事情
     * 1.加载初始化配置
     * 2.执行启动程序
     */
    public $serviceManager;
    //初始化App
    public function __construct($serviceManager) {
        $this->serviceManager    =   $serviceManager;
    }
    public static function init($config){

        $smConfig           =   isset($config['service_manager']) ? $config['service_manager'] : array();
        $serviceManager      =   new ServiceManager(new ServiceManagerConfig($smConfig));        
        $serviceManager->setServer('ApplactionConfig', $config);
        //设置线上模式
        $serviceManager->get('error')->setOnline(false);
        $serviceManager->get('module')->init();
        return $serviceManager->get('Application');
    }
    //执行程序
    public  function run(){
        try {
            Timer::setTimeAnchor('start');
            $this->getService('controller')->onDispatch()
            ->display();
        } catch (\Exception $exc) {
            // 此次需要设置开发环境限制，在开发环境下输出异常信息
            $this->getService('exceptionhandle')->printMsg($exc);
        }   
    }
    public function getService($serverName){
        return $this->serviceManager->get($serverName);
    }
}