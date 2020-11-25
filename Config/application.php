<?php

/* 
 * 项目配置文件
 * 
 */
return array(
    'namespaces' => array(
        'Application' => realpath('./App/Application'),
        'xmpush' => realpath('./Library/Xmpush'),
    ),
    'module'    =>   'Application\Module',
);
