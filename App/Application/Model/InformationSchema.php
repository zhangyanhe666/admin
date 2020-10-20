<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Application\Base\Model;
use Library\Application\Common;
class InformationSchema extends Model{
    private $CurDbName;
    public function init() {}
    public function config($db){
        $dbConfig           =   $this->getService('config')->dbConfig->$db;
        $this->CurDbName    =   $this->getDbName($dbConfig);
        $dbConfig['dsn']    =   str_replace($this->CurDbName, 'INFORMATION_SCHEMA', $dbConfig['dsn']);
        $this->setAdapterByConfig($dbConfig);
        return $this;
    }
    public function getTableInfo($table){
        $tableInfo  =    $this->setTable('TABLES')->columns(array(
            'dbname'=>'TABLE_SCHEMA',
            'tbname'=>'TABLE_NAME',
            'engine'=>'ENGINE',
            'createTime'=>'CREATE_TIME',
            'updateTime'=>'UPDATE_TIME',
            'charset'=>'TABLE_COLLATION',
            'comment'=>'TABLE_COMMENT'
            ))->where(array('table_schema'=>$this->CurDbName,'table_name'=>$table))->getRow();
        return $tableInfo;
    }
    public function getColumnInfo($table){
        $columnInfo =   $this->setTable('COLUMNS')->columns(array(
            'name'=>'COLUMN_NAME',
            'default'=>'COLUMN_DEFAULT',
            'columnKey'=>'COLUMN_KEY',
            'isNull'=>'IS_NULLABLE',
            'type'=>'DATA_TYPE',
            'size'=>'CHARACTER_MAXIMUM_LENGTH',
            'charset'=>'COLLATION_NAME',
            'columnType'=>'COLUMN_TYPE',
            'viewType'=>new \Library\Db\Sql\Predicate\Expression('"defaultType"'),
            'sort'=>'ORDINAL_POSITION',
            'param'=>new \Library\Db\Sql\Predicate\Expression('""'),
            'comment'=>new \Library\Db\Sql\Predicate\Expression('if(COLUMN_COMMENT="",COLUMN_NAME,COLUMN_COMMENT)'),
            ))->where(array('table_schema'=>$this->CurDbName,'table_name'=>$table))->getAll()->toArray();
        return Common::arrayResetObj($columnInfo, 'name');
    }
    public function getColumnAll(){        
        $columnInfo =   $this->setTable('COLUMNS')->columns(array(
            'name'=>'COLUMN_NAME',
            'default'=>'COLUMN_DEFAULT',
            'columnKey'=>'COLUMN_KEY',
            'isNull'=>'IS_NULLABLE',
            'type'=>'DATA_TYPE',
            'size'=>'CHARACTER_MAXIMUM_LENGTH',
            'charset'=>'COLLATION_NAME',
            'columnType'=>'COLUMN_TYPE',
            'tablename'=>'TABLE_NAME',
            'EXTRA',
            'viewType'=>new \Library\Db\Sql\Predicate\Expression('"defaultType"'),
            'sort'=>new \Library\Db\Sql\Predicate\Expression('"0"'),
            'param'=>new \Library\Db\Sql\Predicate\Expression('""'),
            'comment'=>new \Library\Db\Sql\Predicate\Expression('if(COLUMN_COMMENT="",COLUMN_NAME,COLUMN_COMMENT)'),
            ))->where(array('table_schema'=>$this->CurDbName))->order('tablename')->getAll()->toArray();
        return $columnInfo;
    }
    public function getKeyColumn($table){
        
        $keyColumn  =   $this->setTable('KEY_COLUMN_USAGE')->columns(array(
            'keyName'=>'CONSTRAINT_NAME',
            'column'=>'COLUMN_NAME',
            'linkDb'=>'REFERENCED_TABLE_SCHEMA',
            'linkTable'=>'REFERENCED_TABLE_NAME',
            'linkColumn'=>'REFERENCED_COLUMN_NAME',
            ))->where(array('table_schema'=>$this->CurDbName,'table_name'=>$table))->getAll()->toArray();
        return Common::arrayResetObj($keyColumn, 'column');
    }
    public function getAllTables($order=''){        
        $allTables  =   $this->setTable('TABLES')->columns(array('TABLE_NAME','TABLE_COMMENT','CREATE_TIME','TABLE_COLLATION'))->where(array('table_schema'=>$this->CurDbName))->order($order)->getAll()->toArray();
        return $allTables;
    }
}