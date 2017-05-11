<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Model;
use Application\Model\SysModel;

class Shumei extends SysModel{
    public $url = "";
    public $offset  =   0;
    public function init(){}
    public function Get() {
        //初始化
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        //返回结果
        return $output;
    }
    public function Post($postData) {
        $data_string = json_encode($postData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($data_string)));  
        $output = curl_exec($ch);
        curl_close($ch);
        //返回结果
        return $output;
    }
    
    public function text($id,$name,$text,$type = "ZHIBO"){
        $this->url  =   "http://api.fengkongcloud.com/v2/saas/anti_fraud/text";
        $postData = array(
            "accessKey"=>"tiEW6dRvitSne0LgN80d", 
            "type"=>$type,
            "data"=>array(
                "tokenId"=>$id,
                "nickname"=>$name,
                "channel"=>"NICKNAME_CHECK",
                "text"=>$text
             )
        );
        $data       =   $this->Post($postData);
        return json_decode($data, true);
    }

    
    public function getComment($num=1000){
        $res    =   $this->getServer('wukong.video_comment')->offset($this->offset)->limit($num)->getAll()->toArray();
        $this->offset   +=  $num;
        return $res;
    }
    
}