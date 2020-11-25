<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;

class Tpl {
	public $unset;
	public $service;
	public function __construct($service){
        $this->service  =   $service;
    }
    public function getService($service){
    	return $this->service->get($service);
    }
	public function view($data){
		$tpl 		=	[];
		foreach ($data as $key=>$value) {
			$tplFormat 	=	$this->getTplFormat($key);
			$value 	=	call_user_func([$this,$tplFormat],$data,$key);
			if(in_array($value,$this->unset)){
				continue;
			}
			$tpl[]  = $value;
		}
		return $tpl;
	}


	public function getTplFormat($key){
		$fun 	=	$this->getService('CustomTableConfig')->showColumns[$key]['viewType'];
		if(method_exists($this, $fun)){
			return $fun;
		}
		return 'defaultType';
	}
}