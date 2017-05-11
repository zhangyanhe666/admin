<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\ServiceManager\Factory;
use Library\ServiceManager\Factory\FactoryInterface;
use Library\Response\ResponseData;
class ResponseDataFactory implements FactoryInterface{
    
    public function createService($serviceManager) {
        $responseData    =   new ResponseData();
        if(isset($serviceManager->get('config')->view['suffix'])){
            $responseData->setSuffix($serviceManager->get('config')->view['suffix']);
        }
        if(isset($serviceManager->get('config')->view['viewPath'])){
            $responseData->setViewPath($serviceManager->get('config')->view['viewPath']);
        }
        return $responseData;
    }
}