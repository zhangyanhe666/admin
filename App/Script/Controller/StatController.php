<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Script\Controller;
use Application\Base\Controller;
use Library\Application\Common;

class StatController extends Controller{
    //日志地址
    const INIT_DIR  =   '/logs/orig/dd2.wukongtv.com/tvclient/init/';
    //格式化日志地址
    const FORMAT_DIR  =   '/logs/orig/dd2.wukongtv.com/tvclient/init/format/';
    //日志文件名          
    const INIT_LOG_FILE =   'dd2.wukongtv.com.tvclient.init.log-';
    public function indexAction(){
        $data   =   array();
        $tmp    =   array();
        $column =   array('a','b','c','d','e');
        var_dump($this->fun3($column));
    }
    public function fun3($array,$str='',$s=0){
        static $list    =   array();
        for($i=$s;$i<count($array);$i++){
            $tmp  =  $str.$array[$i];
            $list[]   =   $tmp;
            $this->fun3($array,$tmp, $i+1);
        }
        return $list;
    }
    public function logAction(){
        set_time_limit(0);
        $date       =   $this->getRequest()->getQuery('date');
        try{
            $readfile   =   $this->initFile($date);
            $writefile  =   $this->formatFile($date);
        }catch(\Exception $e){
            echo $e->getMessage();exit;
        }
       $num        =   0;
        while(!$readfile->eof()){
            $line   =   $readfile->current();
            $linearr=   explode(' ',$line);
            if(isset($linearr[0]) && isset($linearr[6])){
                $ip     =   $linearr[0];
                $parseurl  =   parse_url($linearr[6]);
                if(!empty($parseurl['query'])){
                    $param  =   common::strToArr(urldecode($parseurl['query']),'&','=');
                    //设定默认值
                    $kyes   =   array('id'=>'','f'=>'','oc'=>'','vn'=>'','t'=>'','av'=>'');
                    $param  =   array_merge($param,$kyes);
                    $content = (isset($param['id']) ? $param['id'] : '') ."|f=".$param['f']."|".$param['oc']."|".$param['vn']."|".$ip."|".$param['t']."|".$param['av']."|".$line;
                    $writefile->fwrite($content);
                }
            }
            $readfile->next();
            $num++;
        }
        exit;
    }
    public function initFile($date){
        $filePath   =   self::INIT_DIR.self::INIT_LOG_FILE.$date;
        if(!is_file($filePath)){
            throw new \Exception("文件{$filePath}不存在");
        }
        $file       =   new \SplFileObject($filePath);
        return $file;
    }
    public function formatFile($date){
        $filePath   =   self::FORMAT_DIR.self::INIT_LOG_FILE.$date;
        $file       =   new \SplFileObject($filePath,'ab');
        return $file;
    }

}