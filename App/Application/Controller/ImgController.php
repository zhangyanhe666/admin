<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\Controller;
use Application\Tool\User;
class ImgController extends Controller{
    
    public function uploadAction(){
	$x1 = $this->getRequest()->getPost('_imgX1');
	$x2 = $this->getRequest()->getPost('_imgX2');
	$y1 = $this->getRequest()->getPost('_imgY1');
	$y2 = $this->getRequest()->getPost('_imgY2');
	$w  = $this->getRequest()->getPost('_imgW');
	$h  = $this->getRequest()->getPost('_imgH');
	$thumbW  = $this->getRequest()->getPost('_thumbW');
	$thumbH  = $this->getRequest()->getPost('_thumbH');
        //上传文件对象
        $imgFile    =   $this->getRequest()->getFiles('_imgFile');
        //上传文件类型
        $imgType    =   $this->getImgType($imgFile->type);
        //上传文件后缀
        $EXT        =   '.jpg';
        //临时文件
        if(!empty($x1)){
            $tmpPath    =   $this->config()->tmpFile().$EXT;
            $oldIm      =   call_user_func('imagecreatefrom'.$imgType,$imgFile->tmp_name);
            $newIm      =   imagecreatetruecolor($thumbW, $thumbH);
            imagecopyresampled($newIm, $oldIm, 0, 0, $x1, $y1, $thumbW, $thumbH, $w, $h);
            imagejpeg($newIm, $tmpPath, 100);
        }  else {
            $tmpPath    =   $imgFile->tmp_name;
        }
        try {
            $url    =   $this->uploadToFtp($EXT, $tmpPath);
            return $this->responseSuccess(array('url'=>$url));
        } catch (\Exception $exc) {
            return $this->responseError($exc->getMessage());
        }
    }
    
    private function uploadToFtp($type,$tmp_name){
        $ftp        =   $this->getServer('ftp')->connect($this->getServer('sys.sys_ftp_config')->getItem(7));
        $homePath   =   '/alidata/www/static2.wukongtv.com/';
        $homeUrl    =   'http://static2.wukongtv.com/';
        $path       =   'specialImage/'.date('Ym').'/';
        $remotedir  =   $homePath.$path;
        $filename   =   time().User::userInfo()->id.$type;
        if(!$ftp->has($remotedir)){
            $ftp->mkdir($remotedir);
        }
        $remote_file=   $remotedir.$filename;
        if(!@$ftp->put($remote_file ,$tmp_name,FTP_BINARY)){
            $error      =   error_get_last();
            $msg        =   '文件上传失败，'.$error['message'];
            throw new \Exception($msg);
        }
        return $homeUrl.$path.$filename;
    }


    private function getImgType($imgType){
        $source =   false;
        switch ($imgType){
            case 'image/gif':
                $source =   'gif';
                break;
	    case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source =   'jpeg';
                break;
	    case "image/png":
	    case "image/x-png":
                $source =   'png';
                break;
        }
        return $source;
    }
}