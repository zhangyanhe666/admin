<?php
//异常显示处理
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Tool;
use Library\Application\ExceptionHandle as LibExceptionHandle;
class ExceptionHandle extends LibExceptionHandle{

    public function comment($k){
        try {
            return $this->getService('Tool\TableConfig')->getColumn($k)->get('comment',$k);   
        } catch (\Exception $exc) {
            return '';
        }


    }
    //获取异常信息
    public function getMsg($exc){
        $msg    =   parent::getMsg($exc);
        if( $exc instanceof \Library\Db\Adapter\Exception\InvalidQueryException){
            $errorInfo  =   $exc->getPrevious()->errorInfo;
            $count      =   preg_match('#column \'(.*)\' #', $errorInfo[2],$match);
            if($count == 1){
                $column =   $this->comment($match[1]);
                $msg    =   "字段：“{$column}”数据填写错误\n错误信息：{$msg}";
            }
        }
        return $msg;
    }
}