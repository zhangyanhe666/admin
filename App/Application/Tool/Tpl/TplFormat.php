<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Tool\Tpl;

class TplFormat extends \Application\Tool\Tool{
    public function tplTool(){
        return $this->getServer('Tool\Tpl\TplTool');
    }
    public function tableConfig(){
        return $this->getServer('Tool\TableConfig');
    }
    //列表页处理方法
    public function index(array $item,$tag='td',$class=''){
        $data   = $this->tplTool()->getItem(__FUNCTION__,$item,function($v){
            return htmlspecialchars($v);
        });
        $tpl    =   array_reduce($data,function($v1,$v2) use($tag,$class){
            return $v1."<{$tag} class='{$class}'>{$v2}</{$tag}>";
        },'');
        return $tpl;
    }
    //添加页处理方法
    public function add($item){
       if(empty($item)){
           $item   =   array_column($this->tableConfig()->getColumnList()->toArray(),'default','name');
       }

        $data   = $this->tplTool()->getItem(__FUNCTION__,$item,function($v){
            return htmlspecialchars($v);
        });
        $tpl    =   implode('', $data);
        return $tpl;
    }


    //编辑页处理方法
    public function edit(array $item){
        $data   = $this->tplTool()->getItem(__FUNCTION__,$item,function($v){
            return htmlspecialchars($v);
        });
        $tpl    =   implode('', $data);
        return $tpl;
    }
    //编辑动作处理方法
    public function doEdit(){
        $item   =   $this->getServer('request')->getPost()->toArray();
        $item   =   array_intersect_key($item,$this->tableConfig()->getColumnList()->toArray());
        $data   =   $this->tplTool()->getItem(__FUNCTION__,$item);
        return $data;
    }
    //添加动作处理方法
    public function doAdd(){      
        $item   =   $this->getServer('request')->getPost()->toArray();        
        $item   =   array_intersect_key($item,$this->tableConfig()->getColumnList()->toArray());
        $data   =   $this->tplTool()->getItem(__FUNCTION__,$item);
        return $data;
    }
    //下载处理方法
    public function down($item){
        $data   = $this->tplTool()->getItem(__FUNCTION__,$item,function($v){
            return htmlspecialchars($v);
        });
        return $data;
    }
    
    
    
    //获取列表名称
    public function getMeans(){
        $order      =   $this->getServer('request')->getQuery('order');
        $sort       =   $this->getServer('request')->getQuery('sort','ASC');
        $means      =   array_map(function($v) use($order,$sort){
            $sort   =   !empty($order) && $sort == 'ASC' ? 'DESC' : 'ASC';
            return array('name'=>$v['comment'],'sort'=>$sort);
        },$this->getUseColumns($this->tableConfig()->getShowColumns()));
        return $means;
    }
    //获取数据库查询的字段
    public function getQueryColumns(){
        
        $custom     =   $this->getServer('request')->getQuery('custom');
        if($custom != 'on'){
            $columnSwitch    =   $this->getServer('Model\Custom')->getMeans();
            $this->tplTool()->setHideColumn(array_keys($columnSwitch));
        }
        return array_keys($this->tableConfig()->getColumnList()->toArray());
    }
    //获取当前能使用的列
    public function getUseColumns($means){        
        $custom     =   $this->getServer('request')->getQuery('custom');
        if($custom != 'on' && ($columnSwitch    =   $this->getServer('Model\Custom')->getMeans())){
           $means      =   array_diff_key($means, $columnSwitch);
        }
        return $means;
    }
    //获取列表页搜索框
    public function getSelectInput(){
        $columns    =   $this->tableConfig()->getShowColumns();
        $revColumns =   array_keys($columns);
        $optoin     =   array_combine($revColumns,array_column($columns,'comment'));
        $columnName =   $this->getServer('request')->getQuery('fieldName',array(next($revColumns)));
        $columnVal  =   $this->getServer('request')->getQuery('fieldVal',array(''));
        $res        =   implode('',array_map( function($k,$v) use($optoin){     
            static  $hasDel =   false;
            $delButton  =   $hasDel ? '<span class="btn " style="margin-top:0px;margin-left: -30px;padding:1px 6px; " onclick="$(this).parent().remove()">-</span>' : '';
            $option     =   \Library\Application\Common::option($optoin,$k);
            $select     =   <<<DIV
             <div class="selectDiv" style="width:auto;float:left;"><select id="sel" style="width:auto;float:left;" name="fieldName[]">{$option}</select><input style="width:auto;" type="text" class="form-control" id="" value="{$v}" name="fieldVal[]" placeholder="">{$delButton}</div>
DIV;
            $hasDel =   true;
            return $select;
        },$columnName,$columnVal));
        return $res;
    }


    //菜单列表
    public function menu($pid=0){
        $menuList   =   $this->getServer('Model\Menu')->menuList($pid);
        $str        =   '';     
        
        if(!empty($menuList)){
            foreach ($menuList as $v){
                $li     =   $this->menu($v['id']).$this->childMenu($v['id']);
                $str    .=   <<<LI
                    <li class="hsub"  id="menu-{$v['id']}">
                        <a  href="javascript:void(0);" class="dropdown-toggle">
                            <i class="menu-icon fa fa-pencil-square-o"></i>
                            <span class="menu-text">{$v['name']}</span>
                            <b class="arrow icon-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                        {$li}
                        </ul>
                    </li>
LI;
            }
        }
        return $str;
    }
    public function childMenu($id){
        $str    =   '';
        $childMenu  =   $this->getServer('Model\ChildMenu')->childMenuList($id);
        foreach ($childMenu as $v){
            $str    .=   <<<LI
                <li>
                <a href="{$this->getServer('router')->url(array('control'=>$v['action'].'_'.$v['id'],'action'=>'index'))}">
                 <i class="icon-double-angle-right"></i>
                {$v['name']}
                <br><span style="color:red">{$v['person_in_charge']}</span>
                </a>
                </li> 
LI;
        }
        return $str;
    }
    //开关
    public function switchButton($column,$val){
        $str    =   '';
        if($this->getServer('request')->getQuery('custom') == 'on'){
            $checked   =   $val == 0 ? 'checked' : '';
            $str    =   <<<TD
                    <span onclick="admin.columnSwitch(this)" column="{$column}" val="{$val}">
                    <label>
                        <input type="checkbox" {$checked} style="width:0px;" class="ace ace-switch ace-switch-6">
                        <span class="lbl"></span> 
                    </label>
                    </span>
TD;
        }
       return $str;
    }


}