<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\Controller;
use Library\Application\Common;
class InstantmsgController extends Controller
{
    protected $api;
    public function init() {
        $instantmsgPath =   realpath('./Library/Instantmsg/');
        $configPath     =   $instantmsgPath.'TimRestApiConfig.json';
        $signature      =   $instantmsgPath.'signature/linux-signature64';
        $config         =   json_decode(file_get_contents($configPath),TRUE);
        $private_pem_path=  $instantmsgPath.$config['private_pem_path'];
        Common::library('Instantmsg/TimRestApi.php');
        $this->api = createRestAPI();
	$this->api->init($config["sdkappid"], $config["identifier"]);
        $ret = $this->api->generate_user_sig($config["identifier"], '36000', $private_pem_path, $signature);
        if($ret == null || strstr($ret[0], "failed")){
            echo "获取usrsig失败, 请确保TimRestApiConfig.json配置信息正确\n";exit;
        }
    }
    public function indexAction() {
        $data   =   $this->api->ToolApi('openim_dirty_words', 'get');
        
       
        //列表部分
        $this->viewData()->setVariable('items', $data);//结果集
        $this->viewData()->addTpl('instantmsg/index');
    }
    public function addAction(){
        
       
        $this->viewData()->addTpl('instantmsg/add');
    }
    public function doAddAction() {
        $DirtyWordsList = $this->getRequest()->getPost('words');
        $data   =   array('DirtyWordsList'=>explode(',', $DirtyWordsList));
        $res    =   $this->api->ToolApi('openim_dirty_words', 'add',json_encode($data));
        return $this->responseSuccess($res);
    }
    public function doDeleteAction(){
        $DirtyWordsList = $this->getRequest()->getPost('words');
        $data   =   array('DirtyWordsList'=>explode(',', $DirtyWordsList));
        $res    =   $this->api->ToolApi('openim_dirty_words', 'delete',json_encode($data));
        return $this->responseSuccess($res);
    }
    public function deleteAction() {        
        
       
        //列表部分
        $this->viewData()->setVariable('submitAction', $this->router()->getUrl(array('action'=>'doDelete')));//结果集
        $this->viewData()->addTpl('instantmsg/add');
    }
}