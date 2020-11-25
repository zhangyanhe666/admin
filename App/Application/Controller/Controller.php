<?php

/* 
 * 后台控制器基类
 */
namespace Application\Controller;
use Library\Application\Controller as LibController;
use Application\Tool\Router;
use Application\Tool\User;
class Controller extends LibController{


    public function response(){
        return $this->getService('Response');
    }

    public function view($tpl){
        return $this->response()->template($tpl);
    }

    public function setVariable($k,$v){
        $this->response()->setVariable($k,$v);
        return $this;
    }
    public function titleView(){
        $hasMenu    =   $this->getService('ChildMenu')->getMenu()->count();
        $this->setVariable('hasMenu',$hasMenu);
        return $this->view('title');
    }
    public function menuView(){
        $menuList   =   $this->getService('menu')->getMenuList();
        $this->setVariable('menuList',$menuList);
        return $this->view('menu');
    }
    public function responseView($tpl){
        $username   =   'admin';
        $this->setVariable('username',$username);
        $this->setVariable('title',$this->titleView());
        $this->setVariable('menu',$this->menuView());
        $this->setVariable('content',$this->view($tpl));
        return $this->view('layout');
    }

    public function responseList(){
        // 获取列表中显示的字段集
        $this->setVariable('listColumn',$this->getService('CustomTableConfig')->showColumns);
        $this->setVariable('closeColumn',$this->getService('Custom')->closeColumn);
        $this->setVariable('page',$this->view('lib/page'));
        return $this->responseView('lib/list');
    }
    /**
     * 获取模版对象
     * @Author   zhangyanhe
     * @DateTime 2019-12-13
     * @param    [type]     $template [description]
     * @return   [type]               [description]
     */
    // protected function template($template){
    //     return $this->getService('ModelFactory')->get('Template',true)->setTemplate($template);
    // }

    // protected function json(){
    //     return $this->getService('JsonResponse',true);
    // }

    /**
     * 获取模型对象
     * @Author   zhangyanhe
     * @DateTime 2019-12-13
     * @param    [type]     $name [description]
     * @return   [type]           [description]
     */
    // protected function getModel($name){
    //     return $this->getService('ModelFactory')->get($name);
    // }

    /**
     * 获取布局对象
     * @Author   zhangyanhe
     * @DateTime 2019-12-13
     * @return   [type]     [description]
     */
    // public function layout($content=''){
    //     $leftMenu   =   $this->menuView();
    //     $layout     =   $this->template('layout')
    //                         ->add('leftMenu',$leftMenu)
    //                         ->add('content',$content);
    //     return $layout;
    // }

    /**
     * 递归处理菜单
     * @Author   zhangyanhe
     * @DateTime 2019-12-13
     * @param    integer    $id [description]
     * @return   [type]         [description]
     */
    // protected function menuView($id=0){
    //     $menuList   =   $this->getModel('Menu')->getMenu($id);
    //     if(empty($menuList)){
    //         return '';
    //     }
    //     $list       =   [];
    //     foreach ($menuList as $menu) {
    //         $id                     =   $menu['id'];
    //         $childMenu              =   $this->getModel('ChildMenu')->getMenu($id);
    //         $list[$id]['menu']      =   $this->menuView($id);
    //         $list[$id]['ChildMenu'] =   $this->template('child_menu')->add('menuList',$childMenu);
    //     }
    //     return $this->template('menu')->add('menuList',$menuList)->add('childMenu',$list);
    // }

    //成功的格式化json返回
    // protected function responseSuccess($data=array()){
    //     return $this->json()
    //          ->add('status','Y')
    //          ->add('msg','')
    //          ->add('data',$data);
    // }
    
    //失败的格式化json返回
    // protected function responseError($msg=''){
    //     return $this->json()
    //          ->add('status','N')
    //          ->add('msg',$msg)
    //          ->add('data',array());
    // }


    //检测登陆
    // protected function checkLogin(){
    //     if(!User::isLogin()){
    //         $url    =   $this->router()->url([],[],true);
    //         $this->getService('Cookies')->set('referer',$url);
    //         $this->router()->toUrl(Router::$login);
    //     }
    // }

    //检测安装
    // protected function checkInstall(){
    //     if($this->config()->dbConfig->count() == 0){
    //         $this->router()->toUrl(Router::$install);
    //     }        
    // }
    // //检测权限
    // protected function checkAuth($menuid=''){
    //     $menuid = empty($menuid) ? $this->router()->getMenuId() : $menuid;
    //     if(empty($menuid)){
    //         throw new \Exception("权限检测失败", 1);
    //     }
    //     if(!$this->getModel('AdminUser')->auth($menuid)){
    //         $this->router()->toUrl(Router::$error,array('msg'=>'无权访问'));
    //     }
    // }

    //     //当前菜单对象
    // protected function getMenu(){
    //     return $this->getService('Model\ChildMenu')->getMenu();
    // }
    // //模板处理器对象
    // protected function tplFormat(){
    //     return $this->getService('Tool\Tpl\TplFormat');
    // }
}
