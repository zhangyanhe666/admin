<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
return array(
    'dbConfig'=>__DIR__.'/local.php',
    'error'=>[
        'control'=>'error',
        'action'=>'index'
    ],
    'memcache'=>array(
        'cached'=>FALSE,
        'cacheMap'=>array(
            'default'=>array('host'=>'localhost','port'=>'11211'),
        ),
    ),
    'router'=>array(
            'control'=>'index',
            'action'=>'index'
    ),
    'view'=>array(
        'viewPath'=>dirname(__DIR__).'/View/',
        'suffix'=>'.phtml',
    ),
);
