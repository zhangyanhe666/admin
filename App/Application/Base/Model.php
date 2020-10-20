<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Base;
use Library\Db\Model as LibModel;
use Application\Tool\Page;
use Application\Tool\User;
class Model extends LibModel{

    public  $service;

    public function __construct($service){
        $this->service  =   $service;
    }

    //初始化数据库对象
    public function init() {
        //如果model 中没有设置数据库会存在问题
        try {
            $this->mem()->setMonitor($this->getCompleteTableName());
        } catch (\Exception $exc) {
        }
        return $this;
    }
    //初始化数据库配置
    public function setAdapter($dbName) {
        $this->setDbKey($dbName);
        return parent::setAdapterByConfig($this->getService('config')->dbConfig->$dbName);
    }

    //缓存对象
    public function mem(){
        return $this->getService('memcache');
    }
    //缓存获取
    public function memGet(){
        $param  =   func_get_args();
        return $this->mem()->get(implode('_', $param));
    }
    //缓存设置
    public function memSet(){
        $param  =   func_get_args();
        $data   =   array_pop($param);
        return $this->mem()->set(implode('_', $param),$data);
    }
    //缓存清理
    public function memDelete(){
        $this->mem()->deleteMonitor();
    }
    //分页获取
    public function paginator($current,$countPerpage=10){
        //初始化分页信息
        $columns        =   $this->sqlSelect()->getRawState('columns');
        $page           =   new Page($this->count(),$current,$countPerpage);
        $this->sqlSelect(TRUE)->columns($columns);
        $res            =   $this->offset($page->offset())->limit($page->countPerpage)->getAll();
        $res->page      =   $page;
        return $res;
    }
    public function add($data){
        if(empty($data)){
            throw new \Exception('添加数据不能为空');
        }
        return $this->insert($data);
    }
    public function edit($id,$data){
        if(empty($data) || empty($id)){
            throw new \Exception('修改数据不能为空');
        }
        return $this->update($data,array('id'=>$id));
    }

    public function adminLog($sql) {
        $logTable   =   'sys.sys_log';
        try {            
            if(!$this->getService('request')->script()){
                if($this->getCompleteTableName() != $logTable 
                        && !empty(User::userInfo()->id)){
                    $info['username']   = User::userInfo()->id;
                    $info['sqlStr']     = $sql;
                    $info['create_time']= date('Y-m-d H:i:s');
                    $this->getService($logTable)->insert($info);
                }    
            }
        } catch (\Exception $exc) {
        }
    }
}