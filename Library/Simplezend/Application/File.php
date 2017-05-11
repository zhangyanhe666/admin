<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Library\Application;

class File{
    public $fileName;
    public static function redirnameall($dir){
        if(false === ($dir_path   = realpath($dir))){
            throw new \Exception('Folder '.$dir.' does not exist');
        }
        $handle = opendir($dir_path);
        $files  =   array();
        while(!false == ($file = readdir($handle))){
            if (!in_array($file, array('.','..')))
                        $files[]    =   $file;               
        }
        closedir($handle);
        return $files;
    }
    public static function rename($oldDir,$newDir){
        if(false === ($dir_path   = realpath($oldDir))){
            throw new \Exception('Folder '.$oldDir.' does not exist');
        }
        $newDir =    realpath('.').DIRECTORY_SEPARATOR.trim($newDir,'/');
        return rename($dir_path,$newDir);
    }
    
    public function conn($fileName){
        $this->fileName     =   $fileName;
        return $this;
    }
    
    //文件信息设置
   /* public function fileName($file){
        $this->fileName     =   $file;
        if($this->exists()){
            $this->fileObject   =   new \SplFileObject($this->getFilePath());
            $this->fileInfo     =   new \SplFileInfo($this->getFilePath());
        }
        return $this;
    }*/
    
    public function getFilePath(){
        if(empty($this->fileName)){
            throw new \Exception('未设置文件路径');
        }
        return $this->fileName;
    }
    //判断是否是空文件
    public function isEmpty(){
        if(!empty($this->get()))return false;        
        return true;
    }
    //文件操作
    public function put($str,$fileType=0){
        //$this->isWritable();
        return file_put_contents($this->getFilePath(), $str,$fileType);
    }
    public function get(){
        //$this->isReadable();
        return file_get_contents($this->getFilePath());
    }
    public function delete(){
        //$this->isWritable();
       // unset($this->fileObject);
       // unset($this->fileInfo);
        if(!@unlink($this->getFilePath())){
            throw new \Exception($this->getFilePath().' Delete failure');
        }
    }
    public function getPerms(){
        return substr(sprintf('%o', fileperms($this->getFilePath())), -4);
    }
    public function filemtime(){
        return filemtime($this->getFilePath());
    }
    public function isWritable(){
        if(!$this->exists()){
            $dir    =   dirname($this->getFilePath());
            if(!file_exists($dir)){
                if(!mkdir($dir,0777,true)){
                    throw new \Exception('create dir '.$dir.' failure');
                }
            }
        }else if($this->fileInfo->isWritable()){
            return true;
        }else{
            throw new \Exception($this->getFilePath().'No write permissions');
        }
    }
    public function isReadable(){
        if(!$this->exists()){
            throw new \Exception($this->getFilePath().'The file does not exist');
        }
        if($this->fileInfo->isReadable()){
            return true;
        }else{
            throw new \Exception($this->getFilePath().'No read permissions');
        }
    }
    public function exists(){
        return file_exists($this->getFilePath());
    }
    //文件扩展操作
    public function putByJson(array $arr){
        $str    = json_encode($arr);
        return $this->put($str);
    }   
    public function putBySerialize(array $arr){
        $str    =   serialize($arr);
        return $this->put($str);
    }
    public function putByArr(array $arr){
        $str    =   var_export($arr,true);
        return $this->put("<?php\nreturn ".$str.";");
    }
    public function getByJson($object=true){
        $arr    =   $this->get();
        return json_decode($arr,$object);
    }
    public function getByArr(){
        //$this->isReadable();
        $arr    =   require $this->getFilePath();
        return $arr;
    }
    public function getBySerialize(){
        $arr    =   $this->get();
        return unserialize($arr);
    }
    
}