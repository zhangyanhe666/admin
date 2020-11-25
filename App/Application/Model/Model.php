<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Library\Db\TableGateway\TableGateway;
use Library\Db\Adapter\Adapter;
use Application\Tool\Page;
class Model{
    public $service;
    public $tableGateway;
    public $tableName;
    public $adapterName;
    public $select;
    public function __construct($service){
        $this->service  =   $service;
        $this->formatTable();
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
        $order  =   $sort   =   '';
        if(isset($this->getService('CustomTableConfig')->menuConfig['config'])){
            $order      =   $this->getService('CustomTableConfig')->menuConfig['config']['orderColumn'];
            $sort       =   $this->getService('CustomTableConfig')->menuConfig['config']['orderSort'];
        }
        $order      =   $this->getService('request')->getQuery('order',$order);
        $sort       =   $this->getService('request')->getQuery('sort',$sort);
        if(empty($order)){
            return $this;
        }
        $this->order($this->tableName.'.'.$order.' '.$sort);
        return $this;
    }

    //设置搜索字段
    public function queryColumns(){
        // $custom     =   $this->getService('request')->getQuery('custom');
        // if($custom != 'on'){
        //     $columnSwitch    =   $this->getService('Model\Custom')->getMeans();
        //     $this->tplTool()->setHideColumn(array_keys($columnSwitch));
        // }
        $columns    =   array_keys($this->getService('CustomTableConfig')->menuConfig['columnList']);
        return $this->columns($columns);
    }

    public function paginator($page,$num){
        //初始化分页信息
        $count          =   $this->getTableGateway()->selectWith($this->getSelect())->count();
        $page           =   new Page($count,$page,$num);
        $this->getSelect()->offset($page->offset())->limit($page->countPerpage);
        $res            =   $this->getAll();
        $res->page      =   $page;
        return $res;
    }

    public function formatTable(){
        if(strpos($this->tableName,'.') != false){
            list($this->adapterName,$this->tableName)   =   explode('.',$this->tableName);
        }
        return $this;
    }

    public function from($tableName){
        $this->tableName    =   $tableName;
        $this->formatTable();
    }



    public function db($adapterName){
        $this->adapterName  =   $adapterName;
    }

    public function getTableGateway(){
        if(empty($this->tableGateway[$this->adapterName])){
            $dbConfig       =   $this->getService('dbConfig')->get($this->adapterName)->toArray();
            if(empty($dbConfig)){
                throw new \Exception('adapter '.$this->adapterName.' is not found', 1);
            }
            $this->tableGateway[$this->adapterName]    =   new TableGateway($this->tableName,Adapter::getInstance($dbConfig));
        }
        return $this->tableGateway[$this->adapterName];
    }

    protected function getService($serviceName,$useAlreadyExists=true){
        return $this->service->get($serviceName,$useAlreadyExists);
    }

    /***********************************
    *   tableGateway代理实现
    ************************************/

    /**
     * 获取select对象
     * @Author   zhangyanhe
     * @DateTime 2020-05-21
     * @return   [type]     [description]
     */
    public function getSelect(){
        if(empty($this->select)){
            $this->select     =   $this->getTableGateway()->getSql()->select();
        }
        return $this->select;
    }

    public function getAll(){
        $all    =   $this->getTableGateway()->selectWith($this->getSelect());
        unset($this->select);
        return $all;
    }

    public function getOne(){
        return $this->getAll()->current();
    }

    /**
     * 查询
     * @Author   zhangyanhe
     * @DateTime 2020-05-21
     * @param    [type]     $where [description]
     * @return   [type]            [description]
     */
    public function select($where=null){
        return $this->getTableGateway()->select($where);
    }

    /***********************************
    *   select代理实现
    ************************************/
    /**
     * 设置列
     * @Author   zhangyanhe
     * @DateTime 2020-05-21
     * @param    [type]     $columns [description]
     * @return   [type]              [description]
     */
    public function columns($columns){
        $this->getSelect()->columns($columns);
        return $this;
    }

    public function order($order){
        $this->getSelect()->order($order);
        return $this;
    }
}