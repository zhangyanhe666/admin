<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Response\Resolve;

class JsonResponse implements \Library\Response\ResponseInterface{
    public $access      =   array();
    public $service;
    public function __construct($service) {
        $this->service      =   $service;
    }
    public function getResponseData(){
        return $this->service->get('responseData');
    }
    public function result(){
        echo json_encode($this->getResponseData()->vars);
    }
    
}