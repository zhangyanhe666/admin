<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\ServiceManager\Factory;
use Library\ServiceManager\Factory\FactoryInterface;
use Library\Application\Config;
class ConfigFactory implements FactoryInterface{
    public function createService($serviceManager) {
        $appConfig   	=   $serviceManager->get('module')->getConfig();
        $config     	=   new Config($appConfig);
        return $config;
    }
}