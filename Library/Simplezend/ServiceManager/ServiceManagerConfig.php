<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\ServiceManager;
use Library\Tool\ToolArray;
class ServiceManagerConfig{
    //使用工厂实例对象
    protected $factories = array(
                'cache'=>'\Library\ServiceManager\Factory\CacheFactory',                
                'controller'=>'\Library\ServiceManager\Factory\ControllerFactory',      
                'responseData'=>'\Library\ServiceManager\Factory\ResponseDataFactory',
                'module'=>'\Library\ServiceManager\Factory\ModuleFactory',
                'config'=>'\Library\ServiceManager\Factory\ConfigFactory',
            );
    //直接实例对象
    protected $instances = array(
                 'request'=>'\Library\Application\Request',
                 'autoload'=>'\Library\Loder\Autoload',
                 'curl'=>'\Library\Http\Curl',
                 'cookies'=>'\Library\Application\Cookies',
                 'file'=>'\Library\Application\File',
                 'pinyin'=>'\Library\Application\Pinyin',
                 'ftp'=>'\Library\Application\Ftp',
                 'excel'=>'\Library\Excel\SpreadsheetExcelReader',
                 'error'=>'\Library\Application\Error',
                 'pinyin'=>'\Library\Application\Pinyin'
            );
    //实例对象并赋予service对象
    protected $instancesService =   array(
                    'memcache'=>'Library\Cache\Memcache',
                    'router'=>'\Library\Application\Router',
                    'exceptionhandle'=>'\Library\Application\ExceptionHandle',
                    'template'=>'\Library\Response\Resolve\TemplateResponse',                  
                    'jsonresponse'=>'\Library\Response\Resolve\JsonResponse',
                    'Application'=>'\Library\Application\Application',
                );
    protected $customService    =   [];
    protected $config;
    public function __construct(array $config = array())
    {
        $this->config   =   ToolArray::merge([
            'factories'=>$this->factories,
            'instances'=>$this->instances,
            'instancesService'=>$this->instancesService,
            'customService'=>$this->customService,
        ],$config);
    }
    public function configureServiceManager(ServiceManager $serviceManager){
        foreach($this->config['factories'] as $k=>$v){
            $serviceManager->setFactorie($k,$v);
        }
        foreach($this->config['instances'] as $k=>$v){
            $serviceManager->setInstanceClass($k,$v);
        }
        foreach($this->config['instancesService'] as $k=>$v){
            $serviceManager->setInstanceService($k,$v);
        }
        foreach($this->config['customService'] as $v){
            $serviceManager->setCustomService($v);
        }
    }
}
