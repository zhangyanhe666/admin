<?php

/* 
 * 项目配置文件
 * 
 */
return array(
    //项目配置; 注:数组第一个元素为默认项目,该项目可以不加项目名直接访问,其他项目需要加项目名进行访问
    'project'=>array(
        'Application',
        'Script',
    ),
    //项目地址配置
    'systemRoot'=>'App',
    
    'config'=>'Config/application.php'
    /*
    //数据库配置
    'dbConfig'=>__DIR__.'/local.php',
    //memcache配置
    'memcache'=>array(
        'cached'=>true,
        'cacheMap'=>array(
            'default'=>array('host'=>'localhost','port'=>'11211'),
            'index'  =>array('host'=>'localhost','port'=>'11211'),
        ),
    ),
     //错误页配置
    'error'=>array(
      'uri'=>'application/error/index',  
    ),
     //路由配置
    'router'=>array(
        'default'=>array(
            'control'=>'index',
            'action'=>'index'
        ),
    ),
    //模板配置
    'view'=>array(
        'viewPath'=>dirname(__DIR__).'/View/',
        'suffix'=>'.phtml',
    ),*/
);
