<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Application;
use Library\ServiceManager\ServiceManager;
use Library\ServiceManager\ServiceManagerConfig;
use Library\Application\Common;
class Application{
    public $serverManager;
    //初始化App
    public function __construct($serverManager) {
        $this->serverManager    =   $serverManager;
    }
    public static function init($config){
        //设置是否报错
        Common::Error(!isset($config['production']) || $config['production'] == true ? FALSE : TRUE);
        $serverManager    =   new ServiceManager(new ServiceManagerConfig($config));
        return $serverManager->get('Application');
    }
    //执行程序
    public  function run(){
        try {
            Common::setTimeAnchor('start');
            $control    =   $this->serverManager->get('controller');
            $control->init();
            $control->onDispatch()->result();
        } catch (\Exception $exc) {
            $this->serverManager->get('exceptionhandle')->printMsg($exc);
        }   
    }
}