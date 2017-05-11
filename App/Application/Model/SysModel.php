<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Application\Base\Model;
class SysModel extends Model{
    
    protected function tplFormat(){
        return $this->getServer('Tool\Tpl\TplFormat');
    }
    protected function tableConfig(){
        return $this->getServer('Tool\TableConfig');
    }
    //获取request对象
    protected function getRequest(){
        return $this->getServer('request');
    }
    //获取列表页数据
    public function getIndexList($defaultNum=10){  
        //获取分页列表数据
        $items      =  $this->queryColumns()->queryWhere()->queryOrder()->joinTable()//->getSqlString();
                            ->paginator($this->getRequest()->getQuery('page',1),$this->getRequest()->getQuery('page_num',$defaultNum));
        return $items;
    }
    //连表查询
    public function joinTable(){
        $linkTables =   $this->tableConfig()->getLinkTables();
        if(!empty($linkTables)){
            foreach ($linkTables as $v){
                $this->join($v->joinTable,$v->on,$v->columns,'left');
            }
        }
        return $this;
    }
    //搜索排序
    public function queryOrder(){
        $order      =   $this->tableConfig()->getCustomConfig()->get('orderColumn','id');
        $sort       =   $this->tableConfig()->getCustomConfig()->get('orderSort','desc');
        $order      =   $this->getRequest()->getQuery('order',$order);
        $sort       =   $this->getRequest()->getQuery('sort',$sort);
        $this->order(trim($this->table.'.'.$order.' '.$sort));
        return $this;
    }
    //设置查询条件
    public function queryWhere(){
        $isLike     =   $this->getRequest()->getQuery('isLike');
        $source     =   $this->getRequest()->getQuery('source');
        $columnName =   $this->getRequest()->getQuery('fieldName');
        $columnVal  =   $this->getRequest()->getQuery('fieldVal');
        $sign       =   $this->getRequest()->getQuery('sign');
        if(!empty($columnVal)){
            $columnVal  =   array_map(function($v){return trim($v);},$columnVal);
        }
        $logic      =   $this->getRequest()->getQuery('logic', \Library\Db\Sql\Predicate\Predicate::OP_AND); 
        $where      =   array();                
        if(!empty($columnName)){
            $wmap   =   array_map(function($name,$val) use($isLike,$source){
                $w      =   array();
                static  $key    =   0;                
                if(empty($source) && !empty($this->tableConfig()->getLinkTables()->$name)){
                    $alias      =   $this->tableConfig()->getLinkTables()->{$name}->alias;
                    $linkName   =   $this->tableConfig()->getLinkTables()->{$name}->linkValue;
                    if(!empty($linkName)){
                        $name       =   $alias.'.'.$linkName;
                    }  else {
                    $name   =   $this->table.'.'.$name;    
                    }
                }else{
                    $name   =   $this->tableConfig()->columnToLinkColumn($name);
                }
                if(!empty($isLike)){
                    $w['key']   =   $name;
                    $w['val']   =   $val;
                }else{
                    $w['key']   =   $key;
                    $key++;
                    $w['val'] =  new \Library\Db\Sql\Predicate\Like($name,"%".$val."%");
                }
                return $w;
            },$columnName,$columnVal);
            $where      =   array_column($wmap,'val','key');
        }
        !empty($this->getServer('Model\ChildMenu')->getMenu()->attach) && $where[]    =     $this->getServer('Model\ChildMenu')->getMenu()->attach;        
        if(!empty($sign)){
            $column     =   $this->tableConfig()->getColumnType('sign');
            if(!empty($column)){
                $where[$column] =   $sign;
            }
        }
        $this->where($where,$logic);
        return $this;
    }
    //设置搜索字段
    public function queryColumns(){
        $this->columns($this->tplFormat()->getQueryColumns());
        return $this;
    }
}
