<?php
namespace Application\Controller;
use Application\Base\Controller;
use Application\Tool\Router;
use Application\Tool\User;
class LoginController extends Controller
{
    public function init() {
        //检测安装
        $this->checkInstall();
    }
    //登录页
    public function indexAction()
    {
        //检测登陆
        if(User::isLogin()){
            $this->router()->toUrl(Router::$index);
        }
        $this->viewData()->setPTpl('login');
    }
    //检测登陆验证
    public function checkloginAction(){
        $username   =   $this->getRequest()->getPost('user');
        $passwd     =   User::password($this->getRequest()->getPost('pass'));
        $userInfo   =   $this->getServer('Model\AdminUser')->login($username,$passwd);
        if(empty($userInfo)){
            return $this->responseError('用户名或密码错误');
        }elseif($userInfo->isdisable == 1){
            return $this->responseError('该用户已被禁用，请联系管理员');
        }
        User::login($userInfo->id, $userInfo->nickname ?  $userInfo->username : $userInfo->nickname, $userInfo->group_id);
    }    
    public function logoutAction(){
        User::unlogin();
        $this->router()->toUrl(Router::$login);
    }



}
