<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;

use Library\Db\Sql\Predicate\Expression;
class Information extends Model{
	public function checkDbExist($dbName){
        $check  =   $this->select(function($select) use($dbName){
            $select->from('SCHEMATA')->columns(array('column'=>new Expression('count(*)')))->where(['schema_name'=>$dbName]);
        })->current()->column;
        return $check > 0 ? true : false;
	}

	/**
     * 执行sql语句
     * @Author   zhangyanhe
     * @DateTime 2019-12-11
     * @param    [type]     $sql [description]
     * @return   [type]          [description]
     */
    public function exec($sql){
        return $this->getAdapter()->query($sql,\Library\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }

}