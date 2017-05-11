<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Application;
class Ftp{
    
    public $ftpconn;
    public $config;
    public function connect($config) {
        if(!isset($config->ip) || !isset($config->username) || !isset($config->passwd)){
            throw new \Exception('ftp参数不完整');
        }
        $this->ftpconn  =   ftp_connect($config->ip);
        $this->login($config->username, $config->passwd);
        $this->config   =   $config;
        return $this;
    }
    public function config(){
        if(empty($this->config)){
            throw new \Exception('ftp未配置');
        }
        return $this->config;
    }
    public function conn(){
        if(empty($this->ftpconn)){
            throw new \Exception('ftp未配置');
        }
        return $this->ftpconn;
    }
    public function login($username, $password){
        return ftp_login($this->conn(), $username, $password);
    }
    public function close(){
        return ftp_close($this->conn());
    }
    public function __destruct() {
        $this->close();
    }
    public function has($path){
        $prevDir    =   dirname($path);
        $list       =   $this->nlist($prevDir);
        if(in_array($path, $list)){
            return true;
        }
        return false;
    }
    public function nlist($dir){
       $filelist   = ftp_nlist($this->conn(), $dir);
       return $filelist;
    }
    public function dirList($dir){
        //return $this->dirListR($dir);
        $dirlist    =   array();
        $filelist   =   $this->rawlist($dir);
        if($filelist){
            foreach($filelist as $k=>$v){
                $fileInfo   =   explode(' ',$v);
                $count      =   0;
                foreach ($fileInfo as $fk=>$fv){
                    if($fv!=''){
                        if($count>7){
                            break;
                        }
                        $count++;           
                    }
                }
                $fileName               =   implode(' ',array_slice($fileInfo, $fk));
                $dirlist[$k]['type']    =   $v{0}   ==  '-' ? $this->checkFileType($fileName) : 'dir';
                $dirlist[$k]['name']    =   $fileName;
            }
        }
        return $dirlist;
    }
    public function dirListR($dir){
        $dirlist    =   array();
        $filelist   =   $this->rawlist($dir);
        if($filelist){
            foreach ($filelist as $k=>$v){
                $fileInfo    =   array_values(array_filter(explode(' ',  str_replace(' 0 ',' - ',$v))));
                $dirlist[$k]['type']   =   $fileInfo[1] == 1 ? $this->checkFileType($fileInfo[8]) : 'dir';
                $dirlist[$k]['name']   =   $fileInfo[8];
                $dirlist[$k]['child']  =   $fileInfo[1] == 1 ? FALSE : $this->dirListR(trim($dir.'/'.$fileInfo[8],'/'));
            }
        }
        return $dirlist;
    }
    public function checkFileType($filename){
        $filetype    =   array(
                    'image' =>  array('bmp','dib','rle','emf','ico','gif','jpg','jpeg','jpe','jif','pcx','dcx','pic','png','tga','tif','tiffxif','wmf','jfif'),
                    'txt'   =>  array('txt','html','htm','phtml','php','css','js','json'),
                );
        $imageSuffix =  strtolower(substr(strrchr($filename, '.'), 1));
        foreach($filetype as $k=>$v){
            if(in_array($imageSuffix,$v)){
                return $k;
            }
        }
        return 'default';
    }
    //切换当前目录的父目录
    public function cdup(){
        return ftp_cdup($this->conn());
    }
    //在 FTP 服务器上改变当前目录
    public function chdir($dir){
        return @ftp_chdir($this->conn(), $dir);
    }
    //修改文件权限
    public function chmod ($dir,$mode){
        return ftp_chmod($this->conn(), $mode, $dir);
    }
    //删除文件
    public function delete($filename){
        return ftp_delete($this->conn(), $filename);
    }
    //请求运行一条 FTP 命令(true,false)
    public function exec($command){
        return ftp_exec($this->conn(),$command);
    }
    //从 FTP 服务器上下载一个文件并保存到本地一个已经打开的文件中
    //mode传输模式只能为 (文本模式) FTP_ASCII 或 (二进制模式) FTP_BINARY 其中的一个。 
    public function fget ($handle,$remote_file,$mode,$resumepos = 0){
        return ftp_fget($this->conn(),$handle,$remote_file,$mode,$resumepos);
    }
    //上传一个已经打开的文件到 FTP 服务器
    //mode传输模式只能为 (文本模式) FTP_ASCII 或 (二进制模式) FTP_BINARY 其中的一个。 
    public function fput ($remote_file,$handle,$mode,$resumepos = 0 ){
        return ftp_fput($this->conn(),$remote_file,$handle,$mode,$resumepos);
    }
    //返回当前 FTP 连接的各种不同的选项设置
    public function get_option($option=FTP_TIMEOUT_SEC){
        return ftp_get_option($this->conn(),$option);
    }
    //从 FTP 服务器上下载一个文件
    public function get($local_file,$remote_file ,$mode,$resumepos=0){
        return ftp_get($this->conn(),$local_file,$remote_file ,$mode,$resumepos);
    }
    //返回指定文件的最后修改时间
    public function mdtm ($remote_file){
        return ftp_mdtm ($this->conn(),$remote_file);
    }
    //建立新目录
    public function mkdir($directory){
        return @ftp_mkdir($this->conn(),$directory);
    }
    //连续获取／发送文件（non-blocking）
    public function nb_continue(){
        return ftp_nb_continue($this->conn());
    }
    //Retrieves a file from the FTP server and writes it to an open file (non-blocking)
    public function nb_fget ($handle,$remote_file,$mode,$resumepos = 0){
        return ftp_nb_fget ($this->conn(),$handle,$remote_file,$mode,$resumepos);
    }
    //Stores a file from an open file to the FTP server (non-blocking)
    public function nb_fput ($remote_file,$handle,$mode,$resumepos = 0 ){
        return ftp_nb_fput ($this->conn(),$remote_file,$handle,$mode,$resumepos);
    }
    //从 FTP 服务器上获取文件并写入本地文件（non-blocking）
    public function nb_get ($local_file,$remote_file ,$mode,$resumepos=0){
        return ftp_nb_get ($this->conn(),$local_file,$remote_file ,$mode,$resumepos);
    }
    //存储一个文件至 FTP 服务器（non-blocking）
    public function nb_put ($remote_file ,$local_file,$mode,$resumepos=0){
        return ftp_nb_put ($this->conn(),$remote_file ,$local_file,$mode,$resumepos);
    }
    //返回当前 FTP 被动模式是否打开
    public function pasv($pasv){
        return ftp_pasv($this->conn(),$pasv);
    }
    //上传文件到 FTP 服务器
    public function put ($remote_file ,$local_file,$mode,$resumepos=0){
        return ftp_put ($this->conn(),$remote_file ,$local_file,$mode,$resumepos);
    }
    //返回当前目录名
    public function pwd (){
        return ftp_pwd ($this->conn());
    }
    //Sends an arbitrary command to an FTP server
    public function raw ($command){
        return ftp_raw ($this->conn(),$command);
    }
    //返回指定目录下文件的详细列表
    public function rawlist($directory){
        return ftp_rawlist($this->conn(),$directory);
    }
    //更改 FTP 服务器上的文件或目录名
    public function rename($oldname ,$newname){
        return ftp_rename($this->conn(),$oldname , $newname);
    }
    //删除 FTP 服务器上的一个目录
    public function rmdir($directory){
        return @ftp_rmdir($this->conn(),$directory);       
    }
    //设置各种 FTP 运行时选项
    public function set_option ($option,$value){
        return ftp_set_option ($this->conn(),$option,$value);
    }
    //向服务器发送 SITE 命令
    public function site ($cmd){
        return ftp_site ($this->conn(),$cmd);
    }
    //返回指定文件的大小
    public function size ($remote_file){
        return ftp_size ($this->conn(),$remote_file);
    }
    //Opens an Secure SSL-FTP connection
    public function ssl_connect($host){
        $this->ftpconn  =    ftp_ssl_connect($host);
    }
    //返回远程 FTP 服务器的操作系统类型
    public function systype (){
        return ftp_systype ($this->conn());
    }
}