<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model;
use Library\Db\Model;
class Log extends Model{
    
    public function write($content,$logname=''){
        $logname    =   $logname.date('Y-m-d').'.log';
        $logpath    =   $this->getService('config')->filePath('Log/'.$logname);
        $content    =   "[".date('Y-m-d H:i:s')."] ".$content."\n";
        file_put_contents($logpath, $content,FILE_APPEND);
    }
}