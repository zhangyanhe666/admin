<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Response\Resolve;
use Library\Application\Common;
class TemplateResponse  implements \Library\Response\ResponseInterface{
    public $content = '';
    public $service;
    public function __construct($service) {
        $this->service      =   $service;
    }
    public function getService($service,$useAlreadyExists=true){
        return $this->service->get($service,$useAlreadyExists);
    }
    public function getRequest(){
        return $this->getService('request');
    }
    public function router(){
        return $this->getService('router');
    }
    public function getResponseData(){
        return $this->getService('responseData');
    }

    /*
     * 可以优化成子模板形式
     * tpl->add(tpl->add(tpl));
     */
    public function result() {
        if(!empty($this->getResponseData()->childrenTemplateList)){
            foreach ($this->getResponseData()->childrenTemplateList as $v){
                $this->content    .=   $this->template($v);
            }
        }
        if($this->getResponseData()->hasTemplate($this->getResponseData()->template)){
            $this->content  =   $this->template($this->getResponseData()->template);
        }
        $templateHtml   =   $this->template($this->getResponseData()->parentTemplate());
        echo $templateHtml;
        return $this;
    }
    public function template($tpl){
        if(!empty($this->getResponseData()->vars)){
            foreach ($this->getResponseData()->vars as $varname => $var){
                $$varname    =   $var;
            }
        }
        ob_start();
        include $this->getResponseData()->template($tpl);
        return ob_get_clean();
    }
}