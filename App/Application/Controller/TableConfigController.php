<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller;
use Application\Base\Controller;
use Library\Application\Common;
use Application\Tool\Html;
class TableConfigController extends Controller{
    public $menuId;
    //初始化
    public function init() {
        //检测安装
        $this->checkInstall();
        //检测登陆
        $this->checkLogin();
        $this->menuId  =  $this->getRequest()->getQuery('menuId');
        if(!empty($this->menuId)){
            //检测权限
            $this->checkAuth($this->menuId);
        }
        $this->viewData()->setVariable('uriParam',array('menuId'=>$this->menuId,'table'=>$this->getRequest()->getQuery('table')));
    }
    public function onDispatch() {
        try {
            return  parent::onDispatch();
        } catch (\Exception $exc) {
            $msg    = $this->getServer('exceptionhandle')->getMsg($exc);
            return !$this->getRequest()->isAjax() ?  $this->router()->error($msg) : $this->responseError($msg);
        }
    }
    public function tableConfig(){
        return $this->getServer('Tool\TableConfig')->setMenuId($this->menuId)->setTable($this->getRequest()->getQuery('table'));
    }
    public function tplTool(){
        return $this->getServer('Tool\Tpl\TplTool');
    }
    //表的编辑页
    public function editAction(){
        //获取外部字段信息
        $linkColumns    =   '';
        if(!empty($this->tableConfig()->getCustomConfig()->linkColumns)){
            $linkColumns     = implode('',array_map(array($this,'getColumnView'), $this->tableConfig()->getCustomConfig()->linkColumns));
        }
        //表列表
        $this->viewData()->setVariable('tableConfig',$this->tableConfig())
                        ->setVariable('linkColumns',$linkColumns)
                        ->setVariable('typeRemark',$this->tplTool()->typeRemark());
    }
    //保存列信息
    public function doEditAction(){
        $this->tableConfig()->columnList  =   new \Library\Application\Parameters($this->getRequest()->getPost('columnList'));
        $this->tableConfig()->getCacheConfig()->config['linkColumns']     =   $this->getRequest()->getPost('linkColumns');
        $this->tableConfig()->getCacheConfig()->config['dispatchmap']     =   $this->getRequest()->getPost('dispatchmap');
        $this->tableConfig()->getCacheConfig()->config['orderColumn']     =   $this->getRequest()->getPost('orderColumn');
        $this->tableConfig()->getCacheConfig()->config['orderSort']       =   $this->getRequest()->getPost('orderSort');
        
        $this->tableConfig()->resetCacheConfig();
    }
    //获取当前数据库下的所有表
    public function getAllTableAction(){
        $optionList     =   array_map(function($v){
            return $v['TABLE_NAME'].'('.$v['TABLE_COMMENT'].')';
        },Common::arrayResetKey($this->tableConfig()->InforSchema()->getAllTables(),'TABLE_NAME'));
        return $this->responseSuccess(Common::option($optionList));
    }
    //获取外部链接表
    public function getLinkTableAction(){
        $optionList =   array_column(array_map(function($v){
            $arr['val'] =   $v['column']."-{$v['linkTable']}";
            return $arr;
        },$this->tableConfig()->getLinkConstraint()),'val','val');
        return $this->responseSuccess(Common::option($optionList));
    }
    //获取表的列信息
    public function tableColumnsAction(){
        $tableName  =   $this->getRequest()->getQuery('tablename');
        $optionList     =   array_map(function($v){
            return $v['name'].'('.$v['comment'].')';
        },$this->tableConfig()->InforSchema()->getColumnInfo($tableName)->toArray());
        return $this->responseSuccess(Common::option($optionList));
    }
    //添加外键约束
    public function addConstraintAction(){
        $post           =   $this->getRequest()->getPost()->toArray();
        $post['linkDb'] =   $this->tableConfig()->getDBServer()->dbKey();
        $post['keyName']=   'custom';
        $this->tableConfig()->getIndexKeyList()->set($post['column'],$post);
        $this->tableConfig()->getColumnList()->{$post['column']}['columnKey']  =   'custom';
        $this->tableConfig()->resetCacheConfig();
        return $this->responseSuccess($post);
    }
    //删除外键约束
    public function delConstrainAction(){
        $column     =   $this->getRequest()->getQuery('column');
        if(isset($this->tableConfig()->getIndexKeyList()->$column)){
            unset($this->tableConfig()->getIndexKeyList()->$column);
            $this->tableConfig()->getColumnList()->$column['columnKey']  =   '';
            $this->tableConfig()->resetCacheConfig();
        }
        
    }
    //添加显示字段
    public function addShowFieldAction(){    
        list($column,$linkTable)    =   explode('-', $this->getRequest()->getPost('linkTable'));
        $linkColumnName             =   $this->getRequest()->getPost('linkColumn');
        $linkColumn                 =   $this->tableConfig()->InforSchema()->getColumnInfo($linkTable)->{$linkColumnName};
        $linkColumn['linkTable']    =   $linkTable;
        $linkColumn['key']          =   $column.'_'.$linkTable.'_'.$linkColumn['name']; 
        return $this->responseSuccess( $this->getColumnView($linkColumn));
    }
    public function delShowFieldAction(){
        $column     =   $this->getRequest()->getQuery('column');
        if(isset($this->tableConfig()->getCustomConfig()->config['linkColumns'][$column]))
            unset($this->tableConfig()->getCustomConfig()->config['linkColumns'][$column]);
        $this->tableConfig()->resetCacheConfig();
    }
    public function getColumnView($column){
        $key        =   $column['key'];
        $option     =   Common::option($this->tplTool()->typeRemark(), $column['viewType'],array('val'=>'val'));
        $isNull     =   $column['isNull'] == 'YES' ?  '<span>是</span>' : '<span style="color:red">否</span>';
        $button     =   Html::button(array('onclick'=>"delShowField(this,'{$key}')"), '删除');
        $str        =   <<<TR
            <tr id="linkColumns_{$key}">  
            <td><span>{$key}</span></td>
            <td><span>{$column['type']}({$column['size']})</span></td>
            <td><input type="text" name="linkColumns[{$key}][comment]" size="13" value="{$column['comment']}"></td>            
            <td><select class="column_select" name="linkColumns[{$key}][viewType]">
                    {$option}
                </select></td>            
            <td><input type="text" name="linkColumns[{$key}][param]" size="100" value="{$column['param']}"></td>
            <td><input type="text" name="linkColumns[{$key}][default]" size="13" value="{$column['default']}"></td>
            <td>{$isNull}</td>
            <td><input type="text" name="linkColumns[{$key}][sort]" size="5" value="0"></td>
            <td>{$button}</td>
            <input type='hidden' name='linkColumns[{$key}][name]' value='{$column['name']}'/>
            <input type='hidden' name='linkColumns[{$key}][type]' value='{$column['type']}'/>
            <input type='hidden' name='linkColumns[{$key}][size]' value='{$column['size']}'/>
            <input type='hidden' name='linkColumns[{$key}][isNull]' value='{$column['isNull']}'/>
            <input type='hidden' name='linkColumns[{$key}][columnKey]' value='{$column['columnKey']}'/>
            <input type='hidden' name='linkColumns[{$key}][charset]' value='{$column['charset']}'/>
            <input type='hidden' name='linkColumns[{$key}][columnType]' value='{$column['columnType']}'/>
            <input type='hidden' name='linkColumns[{$key}][linkTable]' value='{$column['linkTable']}'/>
            <input type='hidden' name='linkColumns[{$key}][key]' value='{$column['key']}'/>
    </tr>
TR;
            return $str;
    }
}