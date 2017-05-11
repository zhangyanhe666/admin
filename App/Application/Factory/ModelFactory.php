<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Factory;
use Application\Model\SysModel;
class ModelFactory{
    public $service;
    public function createModel($service,$dbTable) {
        $model  =   NULL;
        $this->service  =   $service;
        //检测字符串是否符合获取model的规则
        if(strpos(trim($dbTable,'.'),'.') != false){
            list($db,$table)    =   explode('.', $dbTable);
            if(!($model  =   $this->getModel(ucfirst($table),'\Model'))){
                $model          =   new SysModel();
                $model->setTable($table);
            }
            $model->setServiceManager($this->service);
            $model->setAdapter($db);
            $model->init();
        }elseif($model  =   $this->getModel(ucfirst($dbTable))){
            if(method_exists($model, 'setServiceManager')){
                $model->setServiceManager($this->service);
            }
            if(method_exists($model, 'init')){
                $model->init(); 
            }           
        }
        return $model;
    }
    
    public function getModel($modelName,$path=''){
        $model      =   null;
        $project    =   $this->service->get('config')->project;
        $className  =   "\\{$project}{$path}\\{$modelName}";
        if(class_exists($className)){
            $model  =   new $className();
        }
        return $model;
    }
}