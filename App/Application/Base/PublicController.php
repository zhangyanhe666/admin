<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Base;
use Application\Base\Controller;
use Library\Application\Common;
use Library\Db\Sql\Predicate\Expression;
use Application\Tool\Html;
//use Application\Exception\MsgException;
class PublicController extends Controller{
    //初始化设置
    public function init() {

        //检测安装
        $this->checkInstall();
        //检测登陆
        $this->checkLogin();
        //检测权限
        $this->checkAuth();
        //设定router
        Html::setRouter($this->router());
    }
    public function onDispatch() {
        try {
            return  parent::onDispatch();
        } catch (\Exception $exc) {
            $msg    = $this->getServer('exceptionhandle')->getMsg($exc);
            return !$this->getRequest()->isAjax() ?  $this->router()->error($msg) : $this->responseError($msg);
        }
    }
    protected function tableConfig(){
        return $this->getServer('Tool\TableConfig');
    }
    //配置自动匹配模板功能
    protected function template($tpl = '') {
        if(empty($tpl)){
            $control    =   strtolower($this->router()->getControl());
            $tpl        =   "{$control}/{$this->router()->getAction()}";
            if(!$this->viewData()->hasTemplate($tpl)){
                $tpl    =   "{$this->selfTable()->table}/{$this->router()->getAction()}";
            }
        }
        return parent::template($tpl);
    }
    //获取当前使用的表对象
    public function selfTable(){
        return $this->tableConfig()->getDBServer();
    }
    //获取当前库中指定表对象
    public function selfModel($tableName){
        return $this->getServer($this->selfTable()->dbKey().'.'.$tableName);
    }
    /***************
     * 对外Action
     ***************/
    //列表页
    /*************************************************
     *  title，menu，tool，list 
     *  执行时间3S（用户权限检测1S，菜单栏1S，数据获取1S）
     *************************************************/
    public function indexAction() {
        //设定工具栏
        Html::addOption('edit','编辑');
        Html::addOption('copy','复制');
        Html::addOption('delete','删除',array('onclick'=>  'admin.deleteItem(__id);'));
        if('wukong214'  == $this->selfTable()->dbKey()){
            Html::addOption('transfer','迁移到线上',array('exec'=>0));
        }          
        Html::toolDown();
        Html::toolUpload();
        Html::addTool('add','添加',array('href'=>  $this->router()->url(array('action'=>'add'),array(),true)));
        Html::addTool('tableconfig','表结构快速通道',array(
            'href'=>  $this->router()->url(array('control'=>'tableConfig','action'=>'edit'),array('menuId'=>$this->router()->getMenuId())),
            'target'=>'_blank'
        ));
        
        if('on'  == $this->getRequest()->getQuery('custom')){
            Html::addTool('index','完成自定义');
        }  else {            
            Html::addTool('custom','自定义显示',array(
                'href'=>  $this->router()->url(array(),array('custom'=>'on'))
            ));
        }

        if(!empty($this->getServer('Model\ChildMenu')->getMenu()->mem_url)){
             Html::addTool('clearCache','清理缓存',array('exec'=>0));
        }

        $this->viewData()->setVariable('items', $this->selfTable()->getIndexList())//获取分页列表数据
             ->setVariable('columnSwitch', $this->getServer('Model\Custom')->getMeans())//开关
             ->addTpl('lib/list');
    }
    //缓存清理
    public function clearCacheAction(){
        if(empty($this->getMenu()->mem_url)){
            return $this->responseError('未设置缓存清理链接');
        }
        $memurl     =   explode(',', $this->getMenu()->mem_url);
        foreach ($memurl as $v){
            $jsondata   =   file_get_contents($v);
            if(!empty($jsondata)){
                $data   = json_decode($jsondata);        
                if(!$data || !isset($data->status)){
                    return $this->responseError('缓存更新失败');
                }
            }
        }
        return $this->responseSuccess();
    }

    //迁移数据到线上功能
    public function transferAction(){
       $id         =   $this->getRequest()->getQuery('id');
       if(empty($id) || !($item    =   $this->selfTable()->getItem($id))){
            return $this->responseError('迁移失败,没有相应项目');
       }
       unset($item->id);
       $this->getServer('wukong.'.$this->selfTable()->table)->insert((array)$item);
       return $this->responseSuccess();
    }
    //保存单个列编辑信息
    public function doEditColumnAction(){
        $this->selfTable()->edit($this->getRequest()->getPost('id'),$this->tplFormat()->doEdit());
    }
    //编辑页
    public function editAction(){
        $id =   $this->getRequest()->getQuery('id');
        if(empty($id) || !($item =  $this->selfTable()->getItem($id))){
            $this->router()->toUrl(Common::$error);
        }        
        $this->viewData()->setVariable('item',  $item);
        $this->viewData()->addTpl('lib/edit');
    }
    //添加页
    public function addAction(){
        $sign   =   $this->getRequest()->getQuery('sign');
        $column =   $this->tableConfig()->getColumnType('sign');
        if(!empty($sign) && !empty($column)){            
            $this->tableConfig()->setColumnAttr($column,array('default'=>$sign));
        }
        $this->viewData()->addTpl('lib/add');
    }
    //复制
    public function copyAction(){
        $this->editAction();
        $item   =   $this->viewData()->getVariable('item');
        unset($item->id);
        $this->viewData()->setVariable('item',  $item);
        $this->viewData()->setVariable('submitAction',  $this->router()->url(array('action'=>'doAdd')));
    }
    //添加操作
    public function doAddAction(){
        $this->selfTable()->add($this->tplFormat()->doAdd());
    }
    //编辑操作
    public function doEditAction(){
        $this->selfTable()->edit($this->getRequest()->getPost('id'),$this->tplFormat()->doEdit());
    }
    //删除功能
    public function deleteAction(){
        $this->selfTable()->deleteById($this->getRequest()->getQuery('id'));
    }
    //搜索插件功能
    public function searchAction(){
        $fieldName      =   $this->getRequest()->getQuery('fieldName');
        $selectPrompt   =   $this->getRequest()->getQuery('selectPrompt');        
        $linkColumn     =   $this->tableConfig()->getLinkTables()->{$fieldName};
        if($linkColumn->count() == 0){
            return $this->responseError('未搜索到');
        }
        $where          =   array();
        $paramWhere     =   $this->tableConfig()->getColumnParam($fieldName)->where;
        if(!empty($paramWhere)){
            $where[]    =   $paramWhere;
        }        
        $where[]        =   new \Library\Db\Sql\Predicate\Like($linkColumn->linkValue,"%".$selectPrompt."%");
        $map            =   array_column($this->selfModel($linkColumn->linkTable)
                            ->where($where)->limit(10)->getAll()->toArray(),$linkColumn->linkValue,$linkColumn->linkColumn);
        return $this->responseSuccess($map);
    }
    //下载功能
    public function downAction(){
        $data       =   array();
        //请求参数获取
        $items      =   $this->selfTable()->queryColumns()->queryWhere()->queryOrder()->joinTable()->getAll();
        $data[]     =   array_column($this->tableConfig()->getShowColumns(),'comment');
        foreach ($items as $v){
            $data[] =   $this->tplFormat()->down($v->getArrayCopy());
        }
        Common::downCsv($this->tableConfig()->getTableInfo()->comment, $data, $this->getRequest()->getQuery('downcode'));
    }
    //列开关功能
    public function columnSwitchAction(){
        $column =   $this->getRequest()->getQuery('column');
        $val    =   $this->getRequest()->getQuery('val');
        return $this->getServer('Model\Custom')->editCustom($column,$val) ? $this->responseSuccess() : $this->responseError('添加失败');
    }
    public function uploadExcelAction() {
        $excelFile      =   $this->getRequest()->getFiles('excel');
        $this->getServer('excel')->setOutputEncoding('utf-8');
        $this->getServer('excel')->read($excelFile->tmp_name);
        $cells  =   $this->getServer('excel')->sheets[0]['cells'];
        $columnMap  =   array_column($this->tableConfig()->getColumnList()->toArray(),'name','comment');
        $columnKey  =   array_shift($cells);
        $keyNum     =   count($columnKey);
        $columnName =   array_filter(array_map(function($v) use($columnMap){
            return isset($columnMap[$v]) ? $columnMap[$v] : '';
        },$columnKey));
        if(empty($cells)){
             return $this->responseError('数据不能为空');
        }
        if(count($columnName) != $keyNum){
            $diffColumn =   array_diff($columnKey, array_flip($columnName));
            return $this->responseError('文件数据错误,不存在列：'.  implode(',', $diffColumn));
        }
        $cells  =   array_map(function($v) use($keyNum){
            $v  = array_slice($v, 0,$keyNum);
            $v  =   array_pad($v,$keyNum,'');
            return $v;
        },$cells);
        $this->selfTable()->batchInsert($columnName,$cells);
        return $this->responseSuccess();
    }
    public function sortAction(){
        $sort   =   $this->getRequest()->getPost('sort');
        if(!empty($sort) && is_array($sort)){
            $ids    =   array_column($sort,'id');
            $order  =   $this->tableConfig()->getCustomConfig()->get('orderColumn','id');
            $sort   =   $this->tableConfig()->getCustomConfig()->get('orderSort','desc');
            $res    =   $this->selfTable()->where(array('id'=>$ids))->order($order.' '.$sort)->getAll()->toArray();
            $oldsort   =   array_column($res,$order);
            
            if(max(array_count_values($oldsort)) == 1){
                $sortData   =   array_combine( $oldsort,$ids);
            }else{
                //数据存疑重置排序
                $num    =   $this->selfTable()->queryWhere()->joinTable()->count();
                if($num > 3000){
                    return $this->responseError('排序出错，请联系管理员');
                }
                $newsort    =   $sort == '' ? 'desc' : '';
                $allres     =   $this->selfTable()->queryWhere()->joinTable()->order($order.' '.$newsort)->getAll()->toArray();
                $allids     =   array_column($allres,'id');//重置后的排序数据
                //取得未被操作的数据
                $diffids    =   array_diff($allids, $ids);
                //取得被操作的数据
                $interIds   =   array_intersect($allids, $ids);
                //重置数据排序
                $resortIds  =   array_combine(array_keys($interIds), array_reverse($ids));
                $sortData   =   $diffids + $resortIds;
            }
            $sort   =   $this->tableConfig()->getColumnType('sort');
            if(empty($sort)){
                return $this->responseError('无排序字段');
            }
            foreach ($sortData as $k=>$v){
                $this->selfTable()->update(array($sort=>$k),array('id'=>$v));
            }
        }
    }
}