<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model;


class Structure extends SysModel{
    public function init(){}
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
        
        $config     =   $this->getServer('config')->dbConfig->count() > 0 ? array_merge($this->getServer('config')->dbConfig->toArray(),$arr) : $arr;
        $this->getServer('file')->conn($this->getServer('config')->dbConfigPath)->putByArr($config);
        return $this;        
    }
}