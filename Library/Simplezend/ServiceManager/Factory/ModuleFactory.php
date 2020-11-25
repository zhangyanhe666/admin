<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\ServiceManager\Factory;
use Library\ServiceManager\Factory\FactoryInterface;
use Library\Loader\Autoload;
use Library\ServiceManager\ServiceManagerConfig;
class ModuleFactory implements FactoryInterface{
    private $service;
    public function createService($serviceManager) {
        $config         =   $serviceManager->get('ApplactionConfig');
        //配置项目自动加载
        if(isset($config['namespaces'])){
            Autoload::factory( array(
                    'Library\Loader\StandardAutoloader' => array(
                        'namespaces' => $config['namespaces']
                    ),
                )
            );
        }
        if(!isset($config['module'])){
            throw new \Exception("Error module not found", 1);
        }
        $model          =   new $config['module']($serviceManager);
        $smConfig       =   $model->getServiceConfig();
        (new ServiceManagerConfig($smConfig))->configureServiceManager($serviceManager);
        return $model;
    }
   
}

