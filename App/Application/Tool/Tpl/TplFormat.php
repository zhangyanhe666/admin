<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Tool\Tpl;

class TplFormat extends \Application\Tool\Tool{
    public function tplTool(){
        return $this->getService('Tool\Tpl\TplTool');
    }
    public function tableConfig(){
        return $this->getService('Tool\TableConfig');
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
        $item   =   $this->getService('request')->getPost()->toArray();
        $item   =   array_intersect_key($item,$this->tableConfig()->getColumnList()->toArray());
        $data   =   $this->tplTool()->getItem(__FUNCTION__,$item);
        return $data;
    }
    //添加动作处理方法
    public function doAdd(){      
        $item   =   $this->getService('request')->getPost()->toArray();        
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
    
    
    

    //获取数据库查询的字段
    public function getQueryColumns(){
        
        $custom     =   $this->getService('request')->getQuery('custom');
        if($custom != 'on'){
            $columnSwitch    =   $this->getService('Model\Custom')->getMeans();
            $this->tplTool()->setHideColumn(array_keys($columnSwitch));
        }
        return array_keys($this->tableConfig()->getColumnList()->toArray());
    }
    //获取当前能使用的列
    public function getUseColumns($means){        
        $custom     =   $this->getService('request')->getQuery('custom');
        if($custom != 'on' && ($columnSwitch    =   $this->getService('Model\Custom')->getMeans())){
           $means      =   array_diff_key($means, $columnSwitch);
        }
        return $means;
    }


   


}