<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model;


class Structure extends SysModel{
    public function addConfig($id,$dsn,$username,$password){
        $arr    =    array(
                            $id => array(
                                            'driver'            => 'Pdo',
                                            'key'               => $id,
                                            'dsn'               => $dsn,
                                            'username'          => $username,
                                            'password'          => $password,
                                            'driver_options'    => array(
                                                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                                            )
                            ),
                    );
        $sysTable   =   new \Library\Db\Adapter\Adapter($arr[$id]);
        $sysTable->getDriver()->getConnection()->connect();
        
        $config     =   $this->getService('config')->dbConfig->count() > 0 ? array_merge($this->getService('config')->dbConfig->toArray(),$arr) : $arr;
        $this->getService('file')->conn($this->getService('config')->dbConfigPath)->putByArr($config);
        return $this;        
    }
    public function dbConfig($host,$port,$username,$password,$dbname,$key='',$charset='UTF8'){
        $dsn        =   vsprintf('mysql:dbname=%s;host=%s:%s', [$dbname,$host,$port]);
        $dbConfig   =   array(
                            'driver'            => 'Pdo',
                            'key'               => $key,
                            'dsn'               => $dsn,
                            'username'          => $username,
                            'password'          => $password,
                            'driver_options'    => array(
                                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$charset}'"
                            )
                        );
         return $dbConfig;
    }
    public function saveDbConfig($id,$config){
        $dbConfig   =   $this->getService('config')->dbConfig->toArray();
        $dbConfig[$id]  =   $config;
        $this->getService('file')->conn($this->getService('config')->dbConfigPath)->putByArr($dbConfig);
    }
    //检测数据库是否存在
    public function checkDbExist($dbname){
        return $this->setTable('TABLES')->where(['table_schema'=>$dbname])->count();
    }
    public function createDb($dbname){
        $createSql  =   'create database '.$dbname.';';
        $this->getAdapter()->query($createSql,\Library\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }
}