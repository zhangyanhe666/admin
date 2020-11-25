<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;

use Library\Response\Response as LibResponse;
class Response extends LibResponse {

	private $template;

	private $variable;
	private $service;

	public function __construct($service){
		$this->service 	=	$service;
		$this->query 	=	$this->getService('request')->getQuery();
	}

	public function getService($serviceName){
		return $this->service->get($serviceName);
	}

    /**
     * 路由对象
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @return   [type]     [description]
     */
    protected function router(){
        return $this->getService('router');
    }

	/**
	 * 设置变量
	 * @Author   zhangyanhe
	 * @DateTime 2020-05-18
	 */
	public function setVariable($k,$v){
		if($v instanceof LibResponse){
			$this->variable[$k] 	=	$v->fetch();
		}else{
			$this->variable[$k] 	=	$v;
		}
		return $this;
	}

	/**
	 * 设置模版地址
	 * @Author   zhangyanhe
	 * @DateTime 2020-05-18
	 * @param    [type]     $template [description]
	 * @return   [type]               [description]
	 */
	public function template($template){
		$this->template 	=	$this->getService('config')->view->viewPath.$template.$this->getService('config')->view->suffix;
		return $this;
	}

	/**
	 * 显示模版
	 * @Author   zhangyanhe
	 * @DateTime 2020-05-18
	 * @return   [type]     [description]
	 */
	public function display(){
		echo $this->fetch();
	}

	/**
	 * 获取模版地址
	 * @Author   zhangyanhe
	 * @DateTime 2020-05-18
	 * @return   [type]     [description]
	 */
	public function fetch(){
		if(!empty($this->variable)){
            foreach ($this->variable as $varname => $var){
                $$varname    =   $var;
            }
        }

        ob_start();
        include $this->template;
        return ob_get_clean();
	}
	public function tplIndex(){
		return $this->getService('tplIndex');
	}

	/**
	 * 根据菜单id生成菜单连接
	 * @Author   zhangyanhe
	 * @DateTime 2020-10-14
	 * @param    [type]     $control [description]
	 * @param    [type]     $id      [description]
	 * @return   [type]              [description]
	 */
	public function menuLink($control,$id){
		return $this->router()->setControl($control)->setAction('index')->setQuery(['menu_id'=>$id])->url();
	}

	/**
	 * 列表字段篮点击连接
	 * @Author   zhangyanhe
	 * @DateTime 2020-10-14
	 * @return   [type]     [description]
	 */
	public function columnLink($k){
		$k ++;
		return $this->router()->setQuery(['sort'=>$k==$this->query->sort ? -$this->query->sort : $k],TRUE)->url();
	}

	public function index($data,$tag,$class){
		$this->tplIndex()->setTag($tag)->setClass($class)->setData($data)->toTpl();
		$this->getTpl('index',$data);
		$tpl 	=	'';
		foreach ($data as $key=>$value) {
			$tpl  .= "<{$tag} class='{$class}'>".$this->getColumn('index', $key, $value)."</{$tag}>";//$this->getColumn('index', $key, $value);
		}
		// $data   = $this->tplTool()->getItem(__FUNCTION__,$item,function($v){
  //           return htmlspecialchars($v);
  //       });
        // $tpl    =   array_reduce($data,function($v1,$v2) use($tag,$class){
        //     return $v1."<{$tag} class='{$class}'>{$v2}</{$tag}>";
        // },'');
        return $tpl;
	}
}