<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//sql调试 $sql->getSqlStringForSqlObject($select);exit;
namespace Library\Db;
use Library\Db\Adapter\Adapter;
use Library\Db\Sql\Predicate\Expression;
class Model extends TableGateway\MyAbstractTableGateway{
    public  $exec    =   false;
    private $server;
    private $dbKey;
    private $sqlSelect;
    private $batchUpdate    =   '';
    public function init(){}

    public function setDbKey($dbKey){
        $this->dbKey    =   $dbKey;
        return $this;
    }
    public function dbKey(){
        return $this->dbKey;
    }
    public function sqlSelect($exec=FALSE){
        if($exec){
            $this->exec =   true;
        }
        if($this->exec == false){
                $this->sqlSelect        =   $this->getSql()->select();
                $this->exec             =   true;            
        }
        return $this->sqlSelect;
    }
    //合并select方法
    public function __call($name,$arguments){
        /*****
         * 可以将当前类赋值给select，用select去回调当前类
         */
        if(method_exists($this->sqlSelect(), $name)){
            call_user_func_array(array($this->sqlSelect(),$name),$arguments);
        }else{
            throw new \Exception(get_class($this).' Undefined function '.$name);
        }
        return $this;
    }
    public function getRow(){
        $data   =   $this->executeSelect($this->sqlSelect())->current();
        $this->exec     =   FALSE;
        return $data == false ? new \Library\Application\Parameters() : new \Library\Application\Parameters($data->getArrayCopy());
    }
    public function getAll(){
        $data   =   $this->executeSelect($this->sqlSelect());
        $this->exec     =   FALSE;
        return $data;
    }
    public function getItem($id){
        return $this->where(array('id'=>$id))->getRow();
    }
    public function deleteById($id){
        return $this->delete(array('id'=>$id));
    }
    public function getCompleteTableName() {
        return $this->dbKey().'.'.$this->getTable();
    }
    public function getSqlString() {
        return $this->getSql()->getSqlStringForSqlObject($this->sqlSelect());
    }
    public function count(){       
        return $this->getColumn('count(*)');
    }
    public function getColumn($column){
        return $this->columns(array('column'=>new Expression($column)))->getRow()->column;
    }
    //设置Service
    public function setServiceManager($server){
        $this->server  =   $server;
        return $this;
    }
    public function getServer($service,$useAlreadyExists=true){
        return $this->server->get($service,$useAlreadyExists);
    }
    public function batchInsert($columns,$info,$fun=''){
        $values =   array_reduce($info, function($a1,$a2) use($fun){
            $a2     =   is_callable($fun) ? $fun($a2) : array_map(function($vv){
                return addslashes($vv);
            },$a2);
            $a2     =   ',("'.implode('","', $a2).'")';
            return $a1.$a2;
        });
        $sql    =   'insert ignore into '.$this->table.'(`'.implode('`,`', $columns).'`) values'.trim($values,',').$this->batchUpdate;
        //$this->adminLog($sql);
        return $this->adapter->query($sql,  Adapter::QUERY_MODE_EXECUTE);
    }
    public function batchUpdate($columns){
        $update = implode(',', array_map(function($v){
            return "{$v}=VALUES($v)";
        }, $columns));
        $this->batchUpdate  =   ' ON DUPLICATE KEY UPDATE '.$update;
        return $this;
    }

    public function batchInsert1(){
        $param      =   func_get_args();
        $columns    =   array_shift($param);
        $info       =   array();
        //计算数组最小大小
        $min        = array_reduce($param, function($a1,$a2){
            if(is_array($a2)){
                $num    =   count($a2);
                if($a1 == 1 || $num < $a1){
                    return $num;
                }
            }
            return $a1;            
        },1);
        foreach ($param as $v){
            if(!is_array($v)){
                $v  =   array_fill(0, $min, $v);
            }
            $v      =   array_map(function($vv){
                return addslashes($vv);
            },array_slice($v, 0,$min));
            $info   =   !empty($info) ? array_map(function($v1,$v2){
                $v1     = is_array($v1) ? $v1 : array($v1);
                $v1[]   =   $v2;
                return  $v1;
            },$info,$v) : $v;
        }
        $values =   array_reduce($info, function($a1,$a2){
            $a2     =   ',("'.implode('","', $a2).'")';
            return $a1.$a2;
        });
        $sql    =   'insert ignore into '.$this->table.'(`'.implode('`,`', $columns).'`) values'.trim($values,',').$this->batchUpdate;
        $this->adminLog($sql);
        return $this->adapter->query($sql,  Adapter::QUERY_MODE_EXECUTE);
    }
    public function beginTransaction(){
        return  $this->getAdapter()->getDriver()->getConnection()->beginTransaction();
    }
    public function commit(){
        return $this->getAdapter()->getDriver()->getConnection()->commit();
    }
    public function rollBack(){
        return $this->getAdapter()->getDriver()->getConnection()->rollback();
    }
}