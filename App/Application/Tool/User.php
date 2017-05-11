<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Tool;
use Library\Application\Parameters;
class User{
    public static $isSuperAdmin   =   false;
    public static function sessionStart(){
         //开启session；
         if(!isset($_SESSION)) session_start();    
    }
    //退出登录
    public static function unlogin(){
        self::sessionStart();
        session_unset();
        session_destroy();
    }
    //登录
    public static function login($id,$username,$gid){
        self::sessionStart();
        $_SESSION['userinfo']['id']         =   $id;
        $_SESSION['userinfo']['username']   =   $username;
        $_SESSION['userinfo']['gid']        =   $gid;
    }
    //检测用户是否登录
    public static function isLogin(){
        self::sessionStart();
        return isset($_SESSION['userinfo']);
    }
    //密码加密
    public static function password($password){
        return  md5(base64_encode($password));
    }
    public static function userInfo(){
        self::sessionStart();
        return  new Parameters(isset($_SESSION['userinfo']) ? $_SESSION['userinfo'] : array());
    }
}
