<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Application\Tool\User;
class ModelFactory{

    public $service;
    public function __construct($service){
        $this->service  =   $service;
    }
    public function createModel($dbTable) {
        $model  =   NULL;
        $model  =   $this->createModelUseFactory($dbTable);

        if($model === null){
            $model  =   $this->createModelByModel($dbTable);
        }

        if($model === null){
            $model  =   $this->createModelByTable($dbTable);
        }
        if($model === null){
            throw new \Exception("model '{$dbTable}'is not found", 1);
            
        }
        return $model;
    }

    /**
     * 通过工厂创建model
     * @Author   zhangyanhe
     * @DateTime 2020-05-13
     * @param    [type]     $service   [description]
     * @param    [type]     $modelName [description]
     * @return   [type]                [description]
     */
    public function createModelUseFactory($modelName){
        $model  =   null;
        $factorys       =   array(
            'CustomTableConfig'=>function(){
                $tableConfig    =    new CustomTableConfig($this->service);
                $menu_id        =   $this->service->get('request')->getQuery()->menu_id;
                return $tableConfig->setMenuConfig($this->service->get('Custom'),$menu_id);
            },
            'Menu'=>function(){
                $menu    =    new Menu($this->service);
                $menu_id        =   $this->service->get('request')->getQuery()->menu_id;
                return $menu->setMenu($menu_id);
            },
            'Custom'=>function(){
                $menu    =    new Custom($this->service);
                $menu_id        =   $this->service->get('request')->getQuery()->menu_id;
                return $menu->initCloseColumn(User::userInfo()->id,$menu_id);
            },
        );

        if(isset($factorys[$modelName])){
            $model  =   call_user_func($factorys[$modelName]);
        }
        return $model;
    }

    /**
     * 通过表创建model
     * @Author   zhangyanhe
     * @DateTime 2020-05-13
     * @param    [type]     $service   [description]
     * @param    [type]     $modelName [description]
     * @return   [type]                [description]
     */
    public function createModelByTable($modelName){
        if(!strpos($modelName, '.' )){
           return null; 
        }
        return (new Model($this->service))->from($modelName);
    }

    /**
     * 通过模型直接创建model
     * @Author   zhangyanhe
     * @DateTime 2020-05-13
     * @param    [type]     $service   [description]
     * @param    [type]     $modelName [description]
     * @return   [type]                [description]
     */
    public function createModelByModel($modelName){
        $model      =   null;
        $className  =   __NAMESPACE__."\\{$modelName}";
        if(class_exists($className)){
            $model  =   new $className($this->service);
        }
        return $model;
    }
}