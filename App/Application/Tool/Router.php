<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Tool;
use Library\Application\Router as LibRouter;

class Router extends LibRouter{
    
    public function getControl($full=false){
        $control    =   strstr($this->control, '_',true);
        return $control && !$full ? $control : $this->control;
    }

    public function getMenuId(){
        return trim(strstr($this->control, '_'),'_');
    }
}
