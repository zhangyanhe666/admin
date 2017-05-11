<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Response;

class ResponseData{
    public $vars    =   array();
    public $parentTemplate;
    public $childrenTemplateList;
    public $template;
    public $viewPath;
    public $suffix;    
    public $defaultLayout   =   'layout';
    public function setViewPath($path){
        $this->viewPath =   $path;
        return $this;
    }
    public function setSuffix($suffix){
        $this->suffix   =   $suffix;
        return $this;
    }
    public function setVariable($k,$val=''){
        if(is_array($k)){
            if($val != true){
                if(!empty($k)){
                    foreach ($k as $kk=>$v){
                        $this->vars[$kk]  =   $v;
                    }
                }
            }else{
                $this->vars  =   $k;
            }
        }else{
            $this->vars[$k]  =   $val;
        }
        return $this;
    }
    public function resetVariable($var=array()){
        $this->vars =   array();
        return $this;
    }
    public function getVariable($k){
        return isset($this->vars[$k]) ? $this->vars[$k] : '';
    }
    public function setPTpl($tpl){
        $this->parentTemplate    =   $tpl;
        return $this;
    }
    public function setTpl($tpl){
        $this->template    =   $tpl;
        return $this;
    }
    public function parentTemplate(){
        if(empty($this->parentTemplate)){
            return $this->defaultLayout;
        }
        return $this->parentTemplate;
    }
    public function addTpl($tpl){
        $this->childrenTemplateList[]    =   $tpl;
        return $this;
    }
    public function template($tpl){
        if(!$this->hasTemplate($tpl)){
            throw new \Exception('tpl '.$tpl.' is not find');
        }
        return $this->tplPath($tpl);
    }
    public function hasTemplate($tpl){
        return file_exists($this->tplPath($tpl));
    }
    protected function tplPath($tpl){
        return $this->viewPath.$tpl.$this->suffix;
    }
}