<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Library\Db\Adapter\Adapter;
use Library\Response\Resolve\TemplateResponse;
class ModelFactory{
    public $service;
    public $modelList;
    public function __construct($service){
        $this->service  =   $service;
    }
    /**
     * 获取model对象方法
     * @Author   zhangyanhe
     * @DateTime 2019-12-10
     * @param    [type]     $name     [description]
     * @param    boolean    $newModel 是否重新创建新的对象
     * @return   [type]               [description]
     */
    public function get($name,$newModel=false){
        if(isset($this->modelList[$name]) && !$newModel){
            return $this->modelList[$name];
        }
        $db     =   null;
        $model  =   null;
        $table  =   $name;
        if(strpos(trim($name,'.'),'.') != false){
            list($db,$table)    =   explode('.', $name);
        }

        
        $model  =   $this->createModelUseFactory($name);
        
        if($model == null){
            $modelName  =   __NAMESPACE__.'\\'.$table;
            if(class_exists($modelName)){
                $model  =   new $modelName($this,$db);
            }
        }

        if($model == null){
            if(empty($db)){
                throw new \Exception("Model ".$name." Not Find", 1);
            }
            $model  =   new SysModel($this,$name);
        }


        $this->modelList[$name] =   $model;
        return $model;
    }
    
    public function getAdapter($db){
        if(empty($this->service->get('Config')->dbConfig->$db)){
            throw new \Exception("database config ".$db." not find", 1);
        }
        $dbConfig   =   $this->service->get('Config')->dbConfig->{$db}->toArray();
        return Adapter::getInstance($dbConfig);
    }

    public function createModelUseFactory($modelName){
        $model  =   null;
        $factorys       =   array(
            'Template'=>function(){
                return new TemplateResponse($this->service->get('Config')->view->viewPath,$this->service->get('Config')->view->suffix);
            },
            'AdminUser'=>function(){
                $AdminUser  =   new AdminUser($this);
                $superGroupId   =   $this->service->get('Config')->superGroupId;
                $AdminUser->setSuperGroupId($superGroupId);
                return $AdminUser;
            }
        );
        if(isset($factorys[$modelName])){
            $model  =   call_user_func($factorys[$modelName]);
        }
        return $model;
    }
}
