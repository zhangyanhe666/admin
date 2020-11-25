<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;

class TplAdd extends Tpl {

	public $unset;
	public function view($data){
		$data 	=	parent::view($data);
		return array_reduce($data,function($v1,$v2){return $v1."<td class='td-handle'>".$v2."</td>";},'');
	}

	public function f(){

	}
}