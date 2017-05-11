<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Tool;
use xmpush\Sender;
use xmpush\IOSBuilder;
use xmpush\Builder;
class Impush{
    
    const SECRET            =   'hzsuirupnpd7Ve7p3ik9/g==';
    const APP_PACKAGENAME   =   'com.wukongtv.wkremote.client';
    const IOS_BUNDLE_ID     =   'com.wukongtv.remote';
    const IOS_SECRET        =   'I38c7ZNDEGHCU8gACNIytA==';
    
    public $iosSender;
    public $androidSender;
    public function androidSender(){
        if(!$this->androidSender){
            Constants::setPackage(self::APP_PACKAGENAME);
            Constants::setSecret(self::SECRET);
            $this->androidSender   =   new Sender();
        }
        return $this->androidSender;
    }
    public function iosSender(){
        if(!$this->iosSender){
            Constants::setBundleId(self::IOS_BUNDLE_ID);
            Constants::setSecret(self::SECRET);
            $this->iosSender   =   new Sender();
        }
        return $this->iosSender;
    }

    /*
    public function push($title,$desc,$payload,$timeToSend){
        $message    =   $this->androidBuilder($title, $desc, $payload,$timeToSend);
        $this->androidSender()->broadcastAll($message);
    }*/
    public function androidBuilder($title,$desc,$payload,$timeToSend,$appver,$appnover){
        $message  =   new Builder();
        $message->title($title);  // 通知栏的title
        $message->description($desc); // 通知栏的descption
        $message->passThrough(0);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
        $message->payload($payload); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
        $message->notifyId(0); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
        $message->extra(Builder::notifyForeground, 1); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
        if(!empty($appver)){
            $message->extra('app_version', $appver); 
        }
        if(!empty($appnover)){
           $message->extra('app_version_not_in', $appnover);  
        }
        
        if(!empty($timeToSend)){
            $message->timeToSend($timeToSend);
        }
        $message->build();
        return $message;
    }
   /* public function iospush($title,$desc,$payload,$timeToSend){
        $message    = $this->iosBuilder($title,$desc,$payload,$timeToSend);
        $this->iosSender()->broadcastAll($message);
    }*/
    public function iosBuilder($title,$desc,$payload,$timeToSend){
        $message = new IOSBuilder();
        $message->title($title);  // 通知栏的title
        $message->description($desc); // 通知栏的descption
        $message->extra('payload', $payload); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
        $message->badge('1');
        $message->soundUrl('default');
        if(!empty($timeToSend)){
            $message->timeToSend($timeToSend);
        }
        $message->build();
        return $message;
    }
    
    
}