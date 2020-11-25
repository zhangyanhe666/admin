<?php
namespace Application\Controller;

class IndexController extends Controller{
    // public function init() {
    //     //检测安装
    //     $this->checkInstall();
    //     //检测登陆
    //     $this->checkLogin();
    // }




    public function indexAction(){

        return $this->responseView('index/index');
    }
}