<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\ServiceManager;

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
                 'response'=>'\Library\Response\Response',
                 'autoload'=>'\Library\Loder\Autoload',
                 'curl'=>'\Library\Http\Curl',
                 'cookies'=>'\Library\Application\Cookies',
                 'file'=>'\Library\Application\File',
                 'pinyin'=>'\Library\Application\Pinyin',
                 'ftp'=>'\Library\Application\Ftp',
                 'excel'=>'\Library\Excel\SpreadsheetExcelReader',
                 'error'=>'\Library\Application\Error'
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
    protected $config;
    public function __construct(array $config = array())
    {
        $this->config   =   $config;
    }
    public function configureServiceManager(ServiceManager $serviceManager){
        $serviceManager->setServer('systemConfig', $this->config);
        $serviceManager->setFactories($this->factories);
        $serviceManager->setInstanceClasses($this->instances);
        $serviceManager->setInstanceService($this->instancesService); 
        method_exists($serviceManager->get('module'),'init') && $serviceManager->get('module')->init();//可以使用监听模式完成该操作
        method_exists($serviceManager->get('module'),'getCustomService') && $serviceManager->setCustomService($serviceManager->get('module')->getCustomService());
    }
}
