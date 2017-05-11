<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller;
use Application\Base\Controller;
class ErrorController extends Controller{
    public function indexAction() {
        $this->viewData()->setVariable('error', $this->getRequest()->getQuery('msg'));
    }
    public function notFindAction(){
        echo '页面不存在';exit;
    }
    
}
