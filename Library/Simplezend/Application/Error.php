<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Library\Application;

class Error{
    
    public function setPhpError($status = false){
        if (!$status) {            
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        } else {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        }
    }
    
    public function ErrorLog(){
        
    }
}