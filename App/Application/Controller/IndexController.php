<?php
namespace Application\Controller;
use Application\Base\Controller;
use Library\Application\Common;
class IndexController extends Controller{
    public function init() {
        //检测安装
        $this->checkInstall();
        //检测登陆
        $this->checkLogin();
    }
}