<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\ServiceManager\Factory;
use Library\ServiceManager\Factory\FactoryInterface;
class ControllerFactory implements FactoryInterface{
    public function createService($serviceManager) {
        $routerControl  =   $serviceManager->get('router')->getControl();
        $controlName    =   $serviceManager->get('config')->classPath($routerControl,'Controller');
        if(!$controlName){
            $error      =   $serviceManager->get('config')->error;
            $controlName=   $serviceManager->get('config')->classPath($error->control,'Controller');
            if(!$controlName){
                $serviceManager->get('router')->error('页面不存在');
            }
        }
        $control        =   new $controlName();
        $control->setServerManager($serviceManager);
        return $control;
    }
}