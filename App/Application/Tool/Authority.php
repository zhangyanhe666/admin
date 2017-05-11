<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Tool;
use Library\Application\Common;
class Authority{
    
    //权限映射
    public static $authority   =   array(
        'add'          =>   2,
        'edit'         =>   4,
        'del'          =>   8,
        'index'        =>   16,
    );
    //方法映射
    public static $authTypeMap  =   array(
        'add'=>'add',
        'edit'=>'edit',
        'delete'=>'del',
        'doAdd'=>'add',
        'doEdit'=>'edit',
        'index'=>'index',
    );
    //根据routeraction获取权限
    public static function actionAuth($action){
        $type   =   isset(self::$authTypeMap[$action]) ? self::$authTypeMap[$action] : '';
        return  isset(self::$authority[$type]) ? self::$authority[$type] : 0; 
    }
    
    public static function getAuthorityValue($auth){
        return array_filter(self::$authority,function($v) use($auth){
            return ($auth & intval($v)) == $v;
        });
    }
    public static function authSplit($auths){
        $res    =   array();
        if(!empty($auths)){
            $res    =   array_reduce($auths, function($arr,$arr1){
                $authVal    =   array_values(self::getAuthorityValue($arr1['authority']));
                $arr1       =   array_map(function($au) use($arr1){
                    return $arr1['menu_id'].'.'.$au;
                },$authVal);
                return Common::merge($arr, $arr1);
            });
        }
        return $res;
    }
    public static function authMerge($authList){
        $res            =   array();
        if(!empty($authList)){
            foreach ($authList as $v){
                list($k,$num)   =   explode('.', $v);
                !isset($res[$k])    &&  $res[$k]    =   0;
                $res[$k]  +=   $num;
            }        
        }
        return $res;
    }
}
