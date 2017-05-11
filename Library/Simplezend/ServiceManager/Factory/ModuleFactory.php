<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\ServiceManager\Factory;
use Library\ServiceManager\Factory\FactoryInterface;
use Library\Loader\Autoload;
class ModuleFactory implements FactoryInterface{
    private $service;
    public function createService($serviceManager) {
        $this->service  =   $serviceManager;
        $moduleName     =   $this->loadModule();
        $model          =   new $moduleName($serviceManager);
        //配置service
        if(method_exists($model, 'getServiceConfig')){
            $serviceConfig  =   $model->getServiceConfig();
            $serviceManager->setFactories(!isset($serviceConfig['factorices']) ? : $serviceConfig['factorices']);       
            $serviceManager->setInstanceClasses(!isset($serviceConfig['instances']) ? : $serviceConfig['instances']);
            $serviceManager->setInstanceService(!isset($serviceConfig['instancesService']) ? : $serviceConfig['instancesService']);
        }
        //配置项目自动加载
        if(method_exists($model, 'getAutoloadConfig')){
            Autoload::factory($model->getAutoloadConfig());
        }
        return $model;
    }
    public function loadModule(){
        $modulePath =   $this->getService()->get('config')->filePath('Module.php');
        if(file_exists($modulePath)){
            include_once $modulePath;
        }else{
            throw new \Exception('文件不存在:'.$modulePath);
        }
        return '\\'.$this->getService()->get('config')->project.'\Module';
    }
    public function getService(){
        return $this->service;
    }
}

