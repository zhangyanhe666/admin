<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;

use Application\Base\PublicController;
use Application\Tool\Html;
class Mingpai162Controller extends PublicController{
    
    public function indexAction(){
        parent::indexAction();
        Html::addOption('sendMessage', '发送到消息',array('exec'=>0));
    }
    public function sendMessageAction(){
        $item   =   $this->selfTable()->getItem($this->getRequest()->getQuery('id'));
        //消息存储到消息中心
        $info['title']  =   $item->title;
        $info['cover']  =   '';
        $info['desc']   =   $item->info;
        $info['action'] =   'wukongtv://webview?keyloadurl='.urlencode($item->jumpurl);
        $info['dev']    =   $item->dev;
        $this->selfModel('common_message')->add($info);
    }
}