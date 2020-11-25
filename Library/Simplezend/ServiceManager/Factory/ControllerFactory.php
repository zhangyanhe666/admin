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
        $controlName        =   $serviceManager->get('router')->getControl();
        $controlClassName   =   $serviceManager->get('module')->getController($controlName);
        if(!class_exists($controlClassName)){
            throw new \Exception("control ".$controlClassName." not found", 1);
        }
        $control        =   new $controlClassName($serviceManager);
        return $control;
    }
}