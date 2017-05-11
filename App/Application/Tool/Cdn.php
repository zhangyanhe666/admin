<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Tool;

include realpath('./Library/sdk/AliyunCdnSdk/TopSdk.php');
class Cdn{
    public $Aliyun;
    
    public function aliyun(){
        if(!$this->Aliyun){
            $this->Aliyun                   =   new \AliyunClient();
            $this->Aliyun->accessKeyId      =   'Lz375fERj59rnJg4';
            $this->Aliyun->accessKeySecret  =   'zq1QFY7fBc4p4h0pZHNpjQKQ6T7ECE';
            $this->Aliyun->serverUrl        =   'https://cdn.aliyuncs.com';
        }
        return $this->Aliyun;
    }
    
    public function updateCdn($filePath,$objectType='File'){
        if(!in_array($objectType, array('File','Directory')) || empty($filePath)){
            throw new \Exception('类型填写错误,文件路径不能为空');
        }

        //刷新缓存
        $req    =   $this->req($filePath, $objectType);
        $resp   =   $this->aliyun()->execute($req);
        if(isset($resp->Code))
	{	
            return false;
	}
        return true;
    }
    
    public function req($filePath,$objectType){
        //刷新缓存
        $req = new \Cdn20141111RefreshObjectCachesRequest();
        $req->setObjectType($objectType); // or Directory
        $req->setObjectPath($filePath);
        return $req;
    }
}