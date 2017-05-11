<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Script\Controller;
use Application\Base\Controller;
class AccessappController extends Controller
{
    //校队健身操视频是否正常
    public function checkvideoAction(){
        $status =   $this->getRequest()->getQuery('s',0);
        $all    =   $this->getServer('jspt.access_app_video')->where(array('status'=>$status))->getAll();
        foreach ($all as $v){
            if(!$this->check($v['vid'])){
                $this->getServer('jspt.access_app_video')->update(array('status'=>1),array('vid'=>$v['vid']));
            }  else {                
                $this->getServer('jspt.access_app_video')->update(array('status'=>0),array('vid'=>$v['vid']));
            }
        }
        exit;
    }
    public function check($vid){
        $url    =   "http://v.youku.com/v_show/id_{$vid}.html";
        $header =   @get_headers($url);
        if($header == FALSE){
            $this->check($vid);
        }
        if($header[0] == 'HTTP/1.1 200 OK')
            return TRUE;
        return false;
    }
}