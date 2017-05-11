<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
use Application\Exception\JarException;
class JarController extends PublicController
{
    public function doEditAction() {
        $url        =   $this->getUrl();
        $this->getRequest()->getPost()->md5  = md5_file($url);
        return parent::doEditAction();
    }
    public function doAddAction() {
        $url        =   $this->getUrl();
        $this->getRequest()->getPost()->md5  = md5_file($url);
        return parent::doAddAction();
    }
    //获取url
    private function getUrl(){
        $url        =   $this->getRequest()->getPost('url');
        try {
            $config =   $this->urlConfig();
            $serverConfig       =   new \Library\Application\ArrayObject();
            $serverConfig->ip           =   $config->ip;
            $serverConfig->username     =   $config->username;
            $serverConfig->passwd       =   $config->passwd;
            //设置相关文件地址
            $remotePath         =   $config->remoteRootPath.str_replace($config->host, '', $url);
            $url                =   $this->getServer('config')->tmpFile();
            $res    =   $this->getServer('ftp')->connect($serverConfig)->get($url ,$remotePath,FTP_BINARY);
            if(!$res){
                throw new Exception('远程文件下载失败');
            }
        }catch(JarException $e){}
        return $url;
    }
    //配置信息
    private function urlConfig(){
        $config =   array(
            '504'=>array(
                'host'=>'http://static2.wukongtv.com/',
                'remoteRootPath'=>'/alidata/www/static2.wukongtv.com/',
                'ip'=>'10.252.166.240',
                'username'=>'zhangyanhe',
                'passwd'=>'zyh8866162',
            ),
            '332'=>array(
                'host'=>'http://down1.wukongtv.com/',
                'remoteRootPath'=>'/alidata/www/down1.wukongtv.com/',
                'ip'=>'10.168.187.35',
                'username'=>'zhangyanhe',
                'passwd'=>'zyh8866162',
            ),
        );
        $menuid =   $this->router()->getMenuId();
        if($this->getRequest()->host != 'http://wk199.wukongtv.com' || !isset($config[$menuid])){
            throw new JarException('不启用配置');
        }
        return new \Library\Application\Parameters($config[$menuid]);
    }
}
