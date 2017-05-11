<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Controller;
use Library\Application\Common;
class CantvController extends BaseController{
    

    public function indexAction() {
        $model  = $this->getServer('Model\Cantv');
        print_r($this->execScript(array($model,'importVideoCategory')));exit;
    }
    

    public function videoAction() {
        $model  = $this->getServer('Model\Cantv');
        print_r($this->execScript(array($model,'importVideo')));exit;
    }
    public function videoInfoAction() {
        $model  = $this->getServer('Model\Cantv');
        print_r($this->execScript(array($model,'importVideoInfo')));exit;
    }
    public function liveAction() {
        $model  = $this->getServer('Model\Cantv');
        print_r($this->execScript(array($model,'importLive')));exit;
    }
    

    
}