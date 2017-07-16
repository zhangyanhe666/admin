<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Library\Application;
class Config{
    public $systemRoot;
    public $project;
    public $production  =   true;
    public $dbConfig;
    public $dbConfigPath;
    public $error;
    public $memcache;
    public $router;
    public $view;
    public $config;
    public function __construct($config) {
        if(!isset($config['systemRoot'])){
            throw new \Exception('配置文件中不存在 systemRoot');
        }

        if(!isset($config['project']) || !is_array($config['project']) || empty($config['project'])){
            throw new \Exception('配置文件中不存在 project');
        }       
        
        $this->setConfig($config);
    }
    public function setConfig($config){

        foreach ($config as $k=>$v){
            switch ($k){
                case 'project': 
                    $this->setProject($config['project'],isset($config['uri']) ? $config['uri'] : '');
                    break;
                case 'dbConfig': 
                    $this->dbConfig     =   new Parameters(require_once $config['dbConfig']);
                    $this->dbConfigPath =   $config['dbConfig'];
                    break;
                case 'error':
                case 'memcache':
                case 'router':
                case 'view':
                    $this->$k    =  new Parameters($config[$k]);
                    break;
                default :
                    if($k != 'config'){
                        $this->$k    =  $config[$k];
                    }
            }
        }
        
        if(isset($config['config'])){
            $appConfig     = require_once $this->filePath($config['config']);
            $this->setConfig($appConfig);
        }
    }
    public function setProject($project,$uri){
        $uri        =   !empty($uri) ?  strtolower(trim($uri,'/')) : '';
        $projectArr =   array_filter($project,function($v) use($uri){
            return strpos($uri,strtolower($v)) === 0;
        });
        $project        =   empty($projectArr) ? $project : $projectArr;
        $this->project  =   reset($project);
    }
    public function filePath($path){
        return realpath('.').DIRECTORY_SEPARATOR.$this->systemRoot.DIRECTORY_SEPARATOR.$this->project.DIRECTORY_SEPARATOR.ltrim($path,DIRECTORY_SEPARATOR);
    }
    public function classPath($className,$type){
        $className      =   ucfirst($className);
        $type           =   ucfirst($type);
        $controlName    =   implode('\\', [$this->project,$type,$className.$type]);
        if(!class_exists($controlName)){
             return false;
        }
        return $controlName;
    }
    public function tmpFile($tag=''){
        $tmpFileName    =   'Cache/Tmp/tmp.'.$tag.date('s');
        $path           =   $this->filePath($tmpFileName);
        return $path;
    }
}
