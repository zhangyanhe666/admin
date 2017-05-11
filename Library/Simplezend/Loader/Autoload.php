<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Loader;

abstract class Autoload{
    const STANDARDAUTOLOADER    =   'Library\loader\StandardAutoloader';
    protected static $loaders   =   array();
    protected static $standardAutoloader;
    public static function factory($option = null){
        if(null == $option){
            if(!isset(static::$loaders[static::STANDARDAUTOLOADER])){
                $autoload   = static::getStandardAutoloader();
                $autoload->register();
                static::$loaders[static::STANDARDAUTOLOADER]    =   $autoload;
            }
            return ;
        }
        if(!is_array($option) && !($option instanceof \Traversable)){
            throw new \Exception('自动加载配置错误');
        }
        foreach ($option as $class=>$classOption){
            if(!isset(static::$loaders[$class])){
                $autoload   =   static::getStandardAutoloader();
                if(!class_exists($class) && !$autoload->autoload($class)){
                    throw new \Exception('自动加载类"'.$class.'"失败');
                }
                if($class == static::STANDARDAUTOLOADER){
                    $autoload->setOptions($classOption);
                }else{
                    $autoload   =   new $class($classOption);
                }
                $autoload->register();
                static::$loaders[$class]    =   $autoload;
            }else{
                static::$loaders[$class]->setOptions($classOption);
            }
        }
    }
    protected static function getStandardAutoloader(){
        if(null !== static::$standardAutoloader){
            return static::$standardAutoloader;
        }
        if(!class_exists(static::$standardAutoloader)){
            $stdAutoloader  =   substr(strrchr(static::STANDARDAUTOLOADER,'\\'),1);
            require_once __DIR__."/$stdAutoloader.php";
        }
        $loader =   new StandardAutoloader();
        static::$standardAutoloader =   $loader;
        return static::$standardAutoloader;
    }
}
