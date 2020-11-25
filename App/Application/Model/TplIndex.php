<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;

class TplIndex extends Tpl {

	public $unset 	=	[
		'sign',
		'password',
		'notUse',
		'bootstrap',
		NULL,
		'sort',
	];
	public function view($data){
		$data 	=	parent::view($data);
		return array_reduce($data,function($v1,$v2){return $v1."<td class='td-handle'>".$v2."</td>";},'');
	}
	public function defaultType($data,$key){
		$str =	$data[$key];
		if(isset($data['id'])){
			 $str    =   <<<TD
      <span onclick="admin.edit(this,{$data['id']})" style="height:50px;display:block;" field="{$key}" >{$str}</span>
TD;
		}
        return $str;
	}

	public function original($data,$key){
		return $data[$key];
	}

	public function id($data,$key){        
        $editUrl    =   $this->getService('router')->url(array('action'=>'edit'),array($key=>$data[$key]));
        $str        =   <<<TD
                <a   href="{$editUrl}">{$data[$key]}</a>
TD;
        return $str;
	}

	public function shrinkage($data,$key){
		$content    =   str_replace(array("\r","\n","\r\n",),'',str_replace(array(' ','"'),array('&nbsp;','\\"'),$data[$key]));
        $str =   <<<TD
            <input type="button" value="全" onclick=admin.showText(this)>
            <div style="display:none;">{$content}</div>
TD;
        return $str;
	}

	public function interLink($data,$key){
        $str    =   <<<TD
        <a   href="{$data[$key]}">{$data[$key]}</a>
TD;
        return $str;
	}

	public function outLink($data,$key){
		$str    =   <<<TD
        <a   href="{$data[$key]}" target="_blank">{$data[$key]}</a>
TD;
        return $str;
	}

	public function select($data,$key){

        $param      =   $this->tableConfig()->getColumnParam($column);
        $linkColumn =   $this->tableConfig()->getLinkTables()->$column;
        $changeValue      =   '';
        if(!empty($param->map) && isset($param->map[$value])){
            $changeValue  =   $param->map[$value];
        }elseif(!empty($linkColumn)){
            $changeValue  =   $this->item[$linkColumn->newColumn];
        }else{
            $changeValue  =   $value;
        }
        $value    =   $changeValue  ==   $value  ?  $value :   $changeValue."({$value})";
        return $param->linkType == 'outLink' ? $this->outLink($value,$column) : $this->interLink($value,$column);
	}
	public function inputprompt($data,$key){
		return $this->select($data,$key);
	}
	public function judge($data,$key){
		$param      =   $this->tableConfig()->getColumnParam($column);
        empty($param->val) && $param->val   =   $this->judgeMap;
        $checked    =   $param->val[0] == $value ? 'checked' : '';
        $str    =   $value;
        if(isset($this->item['id'])){
        $str    =   <<<TD
        <span onclick="admin.judge(this,{$data['id']})" field="{$key}" val="{$data[$key]}" param="{on:'{$param->val[0]}',off:'{$param->val[1]}'}" >
        <label>
            <input type="checkbox" {$checked} style="width:0px;" class="ace ace-switch ace-switch-6"> 
            <span class="lbl"></span> 
        </label>
        </span>            
TD;
        }
        return $str;
	}
	public function custom($data,$key){
		return $this->original($data,$key);
	}
	public function week($data,$key){
		$value 	=	$data[$key];
		$n  =   date('N');
        if(strpos($value,$n) !== FALSE){
            $value  =   "<i class=\"icon-heart\" style=\"color:red;\" ></i>{$value}";
        }
        return  $value;
	}
}