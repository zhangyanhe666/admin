<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application;
use Application\Model\ModelFactory;
use Library\Application\Config;
class Module{
    public $service;
    public $versionSwitch   =   true;  //版本开关
    public $versions        =   array('','1.3.2'); //版本号数组
    public $versionParam    =   'ver';
    public function __construct($service) {
        $this->service  =   $service;
    }

    public function init(){
        // 初始化数据库
        $dbConfigPath   =   $this->getService('config')->dbConfig;
        if(empty($dbConfigPath)){
            throw new \Exception("dbConfig can not null", 1);
        }
        $this->service->setServer('dbConfig',new Config(include_once $dbConfigPath));
    }

    //获取指定service
    public function getService($server,$useAlreadyExists=true){
        return $this->service->get($server,$useAlreadyExists);
    }

    public function getConfig(){
        return include_once __Dir__.'/Config/application.php';
    }
    public function getController($control){
        return __NAMESPACE__.'\Controller\\'.$control.'Controller';
    }

    public function getServiceConfig(){
        return [
            'instancesService'=>[
                'router'=>'\Application\Tool\Router',
                'exceptionhandle'=>'\Application\Tool\ExceptionHandle',
            ],
            'customService'=>[
                [new ModelFactory($this->service),'createModel']
            ]
        ];
    }
}
