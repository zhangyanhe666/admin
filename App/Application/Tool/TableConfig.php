<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/****
 * 数据表结构处理类
 */


namespace Application\Tool;
use Library\Application\Common;
use Library\Application\Parameters;
class TableConfig extends Tool{
    
    //菜单id
    private $menuId =   null;
    //表名
    private $table;
    //缓存配置
    private $cacheConfig;
    //表信息
    private $tableInfo;
    //列信息
    public $columnList;
    //索引信息
    public $indexKeyList;
    //自定义配置
    public $customConfig;
    //是否有更新
    private $isCheckUpdate = false;
    //列配置
    private $columnParam    =   array();
    //自定义配置
    private $customConfigParam;
    //新增列
    private $linkColumns;
    private $columnType;






    //获取菜单id
    public function getMenuId(){
        if($this->menuId === NULL){
            $this->menuId   =   $this->getService('router')->getMenuId();
        }
        return $this->menuId;
    }
    //设置菜单id
    public function setMenuId($menuId){
        $this->menuId   =   $menuId;
        return $this;
    }
    //设置表名
    public function setTable($table){
        $this->table    =   $table;
        return $this;
    }
    //获取dbServer
    public function getDBServer(){
        return $this->getService($this->getTable());
    }
    //获取表名
    public function getTable(){
        if(empty($this->table)){
            $menu           =   $this->getService('Model\ChildMenu')->getItem($this->getMenuId());
            if($menu->count()){
                $this->table    =   $menu->table_name;
            }  else {
                throw new \Exception('表名配置有误');
            }
        }
        return $this->table;
    }
    public function InforSchema(){
        static $inforSchema =   NULL;
        empty($inforSchema) &&  $inforSchema    =   $this->getService('Model\InformationSchema')->config($this->getDBServer()->dbKey());
        return $inforSchema;
    }
    //获取连表信息
    public function getLinkTables(){
        static $joins      =    array();
        if(empty($joins)){
             $linkTables    = $this->getLinkConstraint();
             if(!empty($linkTables)){
                 $linkColumns   = $this->getLinkColumns()->toArray();
                 foreach ($linkTables as $k=>$v){
                     $linkTable     =   array();
                     $alias         =   $v['column'].'_'.$v['linkTable'];
                     $aTable        =   $this->getDBServer()->table.'.'.$v['column'];
                     $bTable        =   $alias.'.'.$v['linkColumn'];
                     $on            =   $aTable.'='.$bTable;
                     $joinTable     =   array($alias=>$v['linkTable']);
                     $paramValue    =   $this->getColumnParam($v['column'])->value;
                     if(!empty($paramValue)){
                         $linkTable['linkValue']                        =   $paramValue;
                         $linkTable['newColumn']                        =   $alias.'_'.$paramValue;
                         $linkTable['columns'][$linkTable['newColumn']] =   $linkTable['linkValue'];
                     }
                     
                     $columns        =   array_filter($linkColumns,function($column) use($v){
                        return $column['linkTable'] ==  $v['linkTable'];
                     });
                     if(!empty($columns)){
                         foreach ($columns as $cv){
                             $linkTable['columns'][$alias.'_'.$cv['name']]     =   $cv['name'];
                         }
                     }
                     if(!empty($linkTable)){
                        $linkTable['joinTable']    =    $joinTable;
                        $linkTable['on']           =    $on;
                        $linkTable['alias']        =   $alias;
                        $linkTable                 =    array_merge($linkTable, $v);
                        $joins[$k]     =   new \Library\Application\Parameters($linkTable);
                     }
                 }
             }
             $joins =   new \Library\Application\Parameters($joins);
        }
        return $joins;
    }
    
    public function columnToLinkColumn($column){
        $linkTables     =   $this->getLinkTables()->toArray();
        $prfix          =   array_values(array_filter($linkTables,function($v) use($column){
               return isset($v->columns[$column]);
            }));
        if(!empty($prfix)){
            return $prfix[0]->alias.'.'.$prfix[0]->columns[$column];
        }
        return $this->getDBServer()->table.'.'.$column;
    }
    
    public function getLinkConstraint(){
        return array_filter($this->getIndexKeyList()->toArray(),function($v){return !empty($v['linkTable']);});
    }
    //获取可显示列名称
    public function getShowColumns($selfTable=false){
        $notShow    =   array('password','notUse','sign','bootstrap','sort');
        $columns    =   array_filter($this->getColumnList()->toArray(),function($v) use($notShow){return !in_array($v['viewType'], $notShow);});
        return $selfTable ? $columns : Common::merge($columns, $this->getLinkColumns()->toArray());
    }
    public function addColumn($key,$value){
        $default  =   array(
            'comment' => $key,
            'viewType' => 'defaultType',
            'param' => '',
            'default' => '',
            'sort' => 0,
            'name' => $key,
            'type' => '',
            'size' => '',
            'isNull' => '',
            'columnKey' => '',
            'charset' => '',
            'columnType' => '',
            'paramObj' => new \Library\Application\Parameters()      
        );
        $this->getColumnList()->{$key}    =   array_merge($default,$value);
        return  $this;
    }
    //获取自定义配置参数
    public function getCustomConfigParam(){
        if(empty($this->customConfigParam)){
            $this->customConfigParam     =   Common::strToMap($this->getCustomConfig()->dispatchmap);
        }
        return $this->customConfigParam;
    }
    
    //获取列配置参数
    public function getColumnParam($column){
        if(!isset($this->columnParam[$column])){
            $this->columnParam[$column]     =   Common::strToMap($this->getColumn($column)->param);
        }
        return $this->columnParam[$column];
    }
    //获取列信息
    public function getColumn($column){
        return new Parameters($this->getColumnList()->get($column,$this->getLinkColumns()->get($column)));
    }
    //获取表配置
    public function getTableInfo(){       
        if(empty($this->tableInfo)){
            $this->tableInfo     =   new Parameters($this->getCacheConfig()->tableInfo);
            $this->checkUpdate();
        }
        return $this->tableInfo;
    }
    //获取列配置
    public function getColumnList(){        
        if(empty($this->columnList)){
            $this->columnList    =   new Parameters($this->getCacheConfig()->columnList);
            $this->checkUpdate();
        }
        return $this->columnList;
    }
    //获取索引配置
    public function getIndexKeyList(){        
        if(empty($this->indexKeyList)){
            $this->indexKeyList    =   new Parameters($this->getCacheConfig()->constraintList);
            $this->checkUpdate();
        }
        return $this->indexKeyList;
    }
    //获取自定义配置
    public function getCustomConfig(){
        if(empty($this->customConfig)){
            $this->customConfig    =   new Parameters($this->getCacheConfig()->config);
        }
        return $this->customConfig;
    }
    //获取自定义新增列
    public function getLinkColumns(){
       if(empty($this->linkColumns)){
           $this->linkColumns   =   new Parameters($this->getCustomConfig()->linkColumns);
       }
       return $this->linkColumns;
    }
    //获取缓存配置
    public function getCacheConfig(){
        if( empty( $this->cacheConfig ) ){
            $this->cacheConfig  =   $this->getService('Model\CustomTableConfig')->getConfig($this->getMenuId(),$this->getDBServer()->getCompleteTableName());
        }
        return $this->cacheConfig;
    }
    //检测配置更新
    public function checkUpdate(){
        if($this->isCheckUpdate){
            return ;
        }
        $cacheTableInfo =   $this->getCacheConfig()->tableInfo;
        $tableInfo      =   $this->InforSchema()->getTableInfo($this->getDBServer()->table);
        //没有表结构
        if($this->getCacheConfig()->count() == 0){
            $this->tableInfo    =   $tableInfo;
            $columnList         =   $this->InforSchema()->getColumnInfo($this->getDBServer()->table)->toArray();
            $columns    =   array(
                'id'=>'id','password'=>'password','update_time'=>'updatetime','create_time'=>'createtime','cover'=>'img'
            );
            $types      =   array(
                'datetime'=>'datetime'
            );
            foreach ($columnList as $k=>$v){
                if(isset($columns[strtolower($v['name'])])){
                    $v['viewType']  =   $columns[$v['name']];
                }
                if(isset($types[strtolower($v['type'])])){
                    $v['viewType']  =   $types[$v['type']];
                }
                $columnList[$k]     =   $v;
            }
            $this->columnList   =   new Parameters($columnList);
            $this->indexKeyList =   $this->InforSchema()->getKeyColumn($this->getDBServer()->table);
            $this->addCacheConfig();
        //表结构有更新
        }elseif($tableInfo->count() == 0){
            throw new \Application\Exception\MsgException('表不存在');
        }elseif( $cacheTableInfo['createTime']   !=  $tableInfo->createTime ){
            $this->tableInfo    =   $tableInfo;
            //合并indexKey
            $indexKeyList       =   $this->InforSchema()->getKeyColumn($this->getDBServer()->table)->toArray();
            $this->indexKeyList =   new Parameters(Common::merge($indexKeyList,  array_filter($this->getCacheConfig()->constraintList,function($v){return $v['keyName'] == 'custom';})));
            //合并columnList
            $columnList         =   $this->InforSchema()->getColumnInfo($this->getDBServer()->table)->toArray();
            $oldColumnList      =   $this->getCacheConfig()->columnList;
            //保留配置
            $retainColumnList   =   array_intersect_key($oldColumnList,$columnList);
            //处理保留配置
            if(!empty($retainColumnList)){
                foreach ($retainColumnList as $k=>$v){
                    $columnList[$k]['columnKey']    =   $retainColumnList[$k]['columnKey'];
                    $columnList[$k]['viewType']     =   $retainColumnList[$k]['viewType'];
                    $columnList[$k]['sort']         =   $retainColumnList[$k]['sort'];
                    $columnList[$k]['param']        =   $retainColumnList[$k]['param'];
                }
            }
            $this->columnList   =   new Parameters($columnList);
            $this->resetCacheConfig();
        }
        $this->isCheckUpdate  =   TRUE;
    }
    //重置配置
    public function resetCacheConfig(){
        $tableConfig                =   new Parameters();
        $tableConfig->tableInfo     =   $this->getTableInfo();
        $tableConfig->constraintList=   $this->getIndexKeyList();
        $tableConfig->config        =   $this->getCustomConfig();
        $tableConfig->columnList    =    Common::arrayResetKey(Common::arrUasort($this->getColumnList()->toArray(), 'sort'),'name');
        $this->getService('Model\CustomTableConfig')->editItem($this->getMenuId(),$tableConfig->toJson(),$this->getDBServer()->getCompleteTableName());
    }
    //添加配置
    public function addCacheConfig(){
        $tableConfig                =   new Parameters();
        $tableConfig->tableInfo     =   $this->getTableInfo();
        $tableConfig->constraintList=   $this->getIndexKeyList();
        $tableConfig->columnList    =   $this->getColumnList();
        $this->getService('Model\CustomTableConfig')->addItem($this->getMenuId(),$tableConfig->toJson(),$this->getDBServer()->getCompleteTableName());
    }
    public function getColumnType($type){
        if(empty($this->columnType)){
            $this->columnType   =   array_column($this->getColumnList()->toArray(),'viewType','name');
        }
        return array_search($type, $this->columnType);
    }
    public function setColumnAttr($column,$attr){
        $columnAttr     =   $this->getColumnList()->{$column};
        $this->getColumnList()->{$column}   =   array_merge($columnAttr,$attr);
        return $this;
    }
}
