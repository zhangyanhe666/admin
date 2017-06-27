<?php

/* 
 * 后台控制器基类
 */
namespace Application\Base;
use Library\Application\Controller as LibController;
use Application\Tool\Router;
use Application\Tool\User;
class Controller extends LibController{
    //成功的格式化json返回
    protected function responseSuccess($data=array()){
        $this->viewData()
             ->setVariable('status','Y')
             ->setVariable('msg','')
             ->setVariable('data',$data);
        return $this->json();
    }
    
    //失败的格式化json返回
    protected function responseError($msg=''){
        $this->viewData()
             ->resetVariable()
             ->setVariable('status','N')
             ->setVariable('msg',$msg)
             ->setVariable('data',array());
        return $this->json();
    }
    //默认响应数据返回
    protected function defaultResponse() {
        return !$this->getRequest()->isAjax() ? $this->template() : $this->responseSuccess();
    }
    //当前菜单对象
    protected function getMenu(){
        return $this->getServer('Model\ChildMenu')->getMenu();
    }
    //模板处理器对象
    protected function tplFormat(){
        return $this->getServer('Tool\Tpl\TplFormat');
    }
    //检测登陆
    protected function checkLogin(){
        if(!User::isLogin()){
            $url    =   $this->router()->url([],[],true);
            $this->getServer('cookies')->set('referer',$url);
            $this->router()->toUrl(Router::$login);
        }
    }
    //检测安装
    protected function checkInstall(){
        if($this->config()->dbConfig->count() == 0){
            $this->router()->toUrl(Router::$install);
        }        
    }
    //检测权限
    protected function checkAuth($menuid=''){
        $menuid = empty($menuid) ? $this->router()->getMenuId() : $menuid;
        if(!$this->getServer('Model\AdminUser')->auth($menuid)){
            $this->router()->toUrl(Router::$error,array('msg'=>'无权访问'));
        }
    }
}
