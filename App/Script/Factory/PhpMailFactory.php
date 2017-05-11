<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Script\Factory;
use Library\ServiceManager\Factory\FactoryInterface;
use Library\Application\Common;
class PhpMailFactory implements FactoryInterface{
    
    public function createService($service){
        Common::library('PHPMailer/class.phpmailer.php');
        $mail    =   new \PHPMailer();
        $mail->Port       = 465;       
        $mail->CharSet    = 'UTF-8'; 
        $mail->Encoding   = "base64";
        $mail->SMTPAuth   = true;    
        $mail->Host       = "127.0.0.1"; 
        $mail->SMTPSecure = "ssl";
        $mail->From       = "system@tvjianshen.com";
        $mail->FromName   = "悟空邮件系统";
        $mail->WordWrap   = 80;
        $mail->IsHTML(true);
        return $mail;
    }
}
