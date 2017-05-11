<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Tool;
use Application\Exception\MsgException;

class Html{
    public static $optionParam;
    public static $toolParam;
    public static $router;
    
    public static function setRouter($router){
        self::$router   =   $router;
    }

    public static function getRouter(){
        if(self::$router == NULL){
            throw new MsgException('Html 未设定router');
        }
        return self::$router;
    }
    public static function button($param,$name,$class=''){
        $attr   =   '';
        foreach ($param as $k=>$v){
            if(empty($k)){
                $attr   .=   $v.' ';
            }else{
                $attr   .=   $k."=\"{$v}\" ";
            }
            
        }
        $str        =   <<<A
                <a class="btn {$class} btn-sm" {$attr}>{$name}</a>
A;
        return $str;
    }
    public static function addOption($key,$name,$attr=array(),$class=''){
        
        if(empty($attr) || isset($attr['exec'])){
            $url    =   self::getRouter()->url(array('action'=>$key),array('id'=>'__id'));
            if(isset($attr['exec'])){
                $attr   =   array('onclick'=>"admin.execurl('{$url}',{$attr['exec']})");
            }else{
                $attr   =   array('href'=> $url);
            }
        }
        self::$optionParam[$key]   = self::button($attr, $name,$class);
    }
    public static function delOption($key=''){
        if(empty($key))
            self::$optionParam  =   array();
        else
            unset(self::$optionParam[$key]);
    }

    public static function option($data){
        $a  =   '';
        $data       = array_filter($data,function($v){
            return !is_array($v);
        });
        $keys       =   array_map(function($v){return  '__'.$v;},array_keys($data));
        $replace    =   array_values($data);
        if(!empty(self::$optionParam)){
            foreach (self::$optionParam as $v){
                $a  .=  str_replace($keys, $replace, $v);
            }
        }
        return $a;
    }
    
    public static function addTool($key,$name,$attr=array(),$class='btn-primary'){
        if(empty($attr) || isset($attr['exec'])){
            $url    =   self::getRouter()->url(array('action'=>$key));
            if(isset($attr['exec'])){
                $attr   =   array('onclick'=>"admin.execurl('{$url}',{$attr['exec']})");
            }else{
                $attr   =   array('href'=> $url);
            }
        }
        self::$toolParam[$key]   =   self::button($attr, $name,$class);
    }
    public static function delTool($key=''){
        if(empty($key))
            self::$toolParam  =   array();
        else
            unset(self::$toolParam[$key]);
    }

    public static function Tool(){
        $a  =   '';
        if(!empty(self::$toolParam)){
            $tool   = array_chunk(self::$toolParam, 2);
            foreach ($tool as $v){                
                $a  .= implode('', $v).'<br/>';
            }
        }
        return $a;
    }
    public static function toolDown(){
        $downButton =   self::button(array('onclick'=>'admin.down()'),'csv下载','btn-primary');
        $down       =   <<<DIV
                <select id="downcode">
                    <option value="utf8">utf8</option>
                    <option value="gbk">gbk</option>
                </select>{$downButton}
DIV;
        self::$toolParam['down']    =   $down;
    }
    public static function toolUpload(){
        $uploadButton =   self::button(array('onclick'=>"$('#_updateExcel').click()"),'数据上传','btn-primary');
        $upload       =   <<<DIV
         <form id="_updateForm" style='display: none;'  action="uploadExcel" method="post" enctype="multipart/form-data">
            <input type="file" name="excel" id="_updateExcel" style="display:none;">
         </form>
         {$uploadButton}
DIV;
        self::$toolParam['upload']    =   $upload;
    }
        
    public static function checkBoxList($list,$num=0){
        $str    =   '';
        $num++;
        if(!empty($list)){
            $content    =   '';
            foreach ($list as $k=>$v){
                if(is_array($v)){
                    $content    .=   <<<CON
                    <label for="name" class="color-{$num}";  padding-left: 10px;">
                        <b>{$k}</b><input type="checkbox" onclick="admin.checkedboxAll(this)" >
                        <span class="arrow-list down" onclick="admin.itemToggle(this)"></span></label>
CON;
                    $content    .=  self::checkBoxList($v,$num);
                }else{
                    $content    .=   <<<CON
                    <div style="width:330px; float:left; font-size: 14px; padding-left: 5px;">{$v}
                    <input type="checkbox" class="" name="__check[]" value="{$k}"></div>
CON;
                }
            }
            $str    =   <<<DIV
                    <div class="left10"><div class="clear"></div>{$content}</div><div class="clear"></div>        
DIV;
        }        
        return $str;
    }
    
        //开关
    public static function switchButton($column,$val){
        $str    =   '';
        $checked   =   $val == 0 ? 'checked' : '';
        $str    =   <<<TD
                <span onclick="admin.columnSwitch(this)" column="{$column}" val="{$val}">
                <label>
                    <input type="checkbox" {$checked} style="width:0px;" class="ace ace-switch ace-switch-6">
                    <span class="lbl"></span> 
                </label>
                </span>
TD;
                    
       return $str;
    }
}