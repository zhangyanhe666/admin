<?php

//数组处理工具类
namespace Library\Tool;
class ToolArray{
    
    public static function merge($arr,$arr1){
        if(empty($arr)){
            return $arr1;
        }
        if(empty($arr1)){
            return $arr;
        }
        return array_merge($arr,$arr1);
    }
}