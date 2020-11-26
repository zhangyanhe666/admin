<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
use Application\Base\Controller;
use Library\Application\File;
use Library\Application\Common;
class FtpController extends Controller
{
    public $ftp;
    public $servers;
    public $img_url;
    public $img_path;
    public $skey;
    
    public function init(){
        //检测安装
        $this->checkInstall();
        //检测登陆
        $this->checkLogin();
        //检测权限
        $this->checkAuth();
        $server     =   $this->getServer('sys.sys_ftp_config')->getItem($this->getRequest()->getQuery('server',1));
        if($server->count() > 0){
            $this->getServer('ftp')->connect($server)->chdir($server->path);
        }
    }
    //ftp首页
    public function indexAction() {
        $servers    =   array_column($this->getServer('sys.sys_ftp_config')->getAll()->toArray(),'url','id');
        $this->viewData()->setVariable('servers',$servers);
        $this->viewData()->setVariable('ftp',$this->getServer('ftp'));
    }
    //获取文件列表
    public function fileListAction(){
        $items      =   array();
        $dir        =   $this->getRequest()->getQuery('dir','.');
        $filelist   =   $this->getServer('ftp')->dirList($dir);
        if($dir == '.'){
            array_unshift($filelist, array('type'=>'dir','name'=>'.') );
        }
        if(!empty($filelist)){
            if(count($filelist) > 1000){
                return $this->responseError('文件夹内容超过1000条无法显示');
            }
            foreach($filelist as $k=>$v){
                $code   =   'UTF-8';
                if(Common::checkStrCode($v['name']) == 'GB2312'){
                    $v['name']  =   iconv('GBK','UTF-8',$v['name']);
                    $code   =   'GBK';
                }
                $adir   =   trim($dir).'/'.trim($v['name']);
                $dom    =   new \DOMDocument('1.0');
                $span   =   $dom->createElement('span');
                $dirArr    =   $dom->createAttribute('dir');
                $dirArr->value    =   $adir;
                $typeArr   =   $dom->createAttribute('type');
                $typeArr->value   =   $v['type'];
                $codeArr   =   $dom->createAttribute('code');
                $codeArr->value   =   $code;
                $classArr  =   $dom->createAttribute('class');

                switch ($v['type']){
                    case 'dir':
                        break;
                    case 'image':
                        $classArr->value   =   'icon-picture green';
                        break;
                    case 'txt':                        
                        $classArr->value   =  'icon-file-text red';
                        break;
                    case 'default':
                    default :                        
                        $classArr->value   =  'icon-file-text grey';
                        break;
                }
                $span->appendChild($dirArr);
                $span->appendChild($typeArr);
                $span->appendChild($classArr);
                $span->appendChild($codeArr);
                $dom->appendChild($span);
                $items[$v['name']]['name']   =   $dom->saveHTML().$v['name'];
                $items[$v['name']]['type']  =   $v['type'] == 'dir' ? 'folder' : 'item';
                $items[$v['name']]['icon-class']  =   'red';
                $items[$v['name']]['dir']  =   $adir;
                $items[$v['name']]['code']  =   $code;
                 
            }            
            return $this->responseSuccess($items);
        }
        return $this->responseError('空文件夹');
    }
    public function showTxtAction(){
        $file       =   $this->getRequest()->getQuery('filePath');
        $FilePath   =   $this->config()->filePath('Cache/Tmp/tmp.'.date('s'));
        if($this->getServer('ftp')->size($file) == 0){
            return $this->responseError('文件不存在或者是空文件');
        }else{
            $this->getServer('ftp')->get($FilePath ,$file,FTP_BINARY);
            return $this->responseSuccess(array('content'=>$this->getServer('file')->conn($FilePath)->get(),'url'=>$this->getServer('ftp')->config()->url.trim($file,'./')));
        }
    }
    public function updateCdnAction(){
        $filePath   =   $this->getRequest()->getQuery('filePath');
        $type       =   $this->getRequest()->getQuery('type');
        if(!empty($filePath)){
            $this->responseError('参数错误');
        }
        $filePath   =   $this->getServer('ftp')->config()->url.trim($filePath,'./');
        if($type == 'dir'){
            $type   =   'Directory';
            $filePath   .=  '/';
        }else{
            $type   =   'File';
        }
        if(!$this->getServer('Tool\Cdn')->updateCdn($filePath,$type)){
            $this->responseError('缓存更新失败');
        }
    }
    public function updateUrlCdnAction(){
        $url    =   $this->getRequest()->getQuery('url');
        if(!empty($url)){
            $type  =   $url{strlen($url)-1} == '/' ? 'Directory' : 'File';
            if($this->getServer('Tool\Cdn')->updateCdn($url,$type)){
                return $this->responseSuccess();
            }else{
                return $this->responseError('更新链接失败，请检查填写是否正确');
            }
        }else{
            return $this->responseError('链接不能为空');
        }
    }
    public function ftpmkdirAction(){
        $dirname    =   $this->getRequest()->getPost('dirname');
        $dir        =   $this->getRequest()->getPost('dir');
        if(!$this->getServer('ftp')->mkdir($dir.'/'.$dirname)){
            return $this->responseError('文件夹创建失败');
        }        
        return $this->responseSuccess();
    }
    public function uplodeimageAction(){
        $remote_file    =   $this->getRequest()->getQuery('dir').'/'.$this->getRequest()->getFiles('file')->name;
        if(!@$this->getServer('ftp')->put($remote_file ,$this->getRequest()->getFiles('file')->tmp_name,FTP_BINARY)){
            $error      =   error_get_last();
            $msg        =   '文件上传失败，'.$error['message'];
            return $this->responseError($msg);
        }
    }
    public function deleteAction(){
        $filename       =   $this->getRequest()->getQuery('dir');
        $fun            =   $this->getRequest()->getQuery('type') == 'dir' ? 'rmdir' : 'delete';
        if(!$this->getServer('ftp')->$fun($filename)){
            return $this->responseError('删除操作失败');
        }  
        return $this->responseSuccess();
    }
    public function downAction(){
        $FilePath       =   $this->config()->filePath('Cache/Tmp/tmp.'.date('s'));
        $Ofile  =   $file           =   $this->getRequest()->getQuery('dir');
        $code           =   $this->getRequest()->getQuery('code');
        if($code == 'GBK'){
            $file  =   iconv('UTF-8','GBK',$file);
        }
        $this->getServer('ftp')->get($FilePath ,$file,FTP_BINARY);
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: ".filesize($FilePath));
        Header("Content-Disposition: attachment; filename=". basename($Ofile));
        echo $this->getServer('file')->conn($FilePath)->get();
        exit;
    }
}