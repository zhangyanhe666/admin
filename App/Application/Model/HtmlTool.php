<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Library\Application\Common;
class HtmlTool {
	
	private $service;

	private $options 	=	[
		'edit'=>'编辑',
        'copy'=>'复制',
        'delete'=>'删除',
        'transfer'=>'迁移到线上',
	];

	private $tool 	=	[
		'toolDown'=>[],
		'toolUpload'=>[],
		'add'=>[],
		'tableconfig'=>[],
		'index'=>[],
		'custom'=>[],
	];

	private $useOptions;
	private $useTool;
	public function __construct($service){
		$this->service 	=	$service;
	}

	public function useOptions($keys){
		$this->useOption 	=	Common::array_value($this->options,$keys);
		return $this;
	}

	public function useTools($keys){
		$this->useTool 	=	Common::array_value($this->tool,$keys);
		return $this;
	}

	public function getUseOptions(){
		return json_encode($this->useOption);
	}
	public function getService($service){
		return $this->service->get($service);
	}
}