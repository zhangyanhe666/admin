<?php
namespace Application\Model;
use Application\Model\SysModel;
use Library\Application\Parameters;
use Library\Db\Sql\Predicate\In;
use Library\Application\Common;
use Application\Tool\User;
class Menu extends SysModel
{
    public $menus   =   array();
    public $allMenus=   array();
    public function init() {
        $this->setAdapter('sys');
        $this->setTable('sys_menu');
        parent::init();
    }
    //获取有权限的所有菜单
    public function getMenus($id){
        if(!isset($this->menus[$id])){
            $childList          =   $this->getServer('Model\ChildMenu')->getChildMenus($id);
            $parentids          =   array_unique(array_column($childList,'parent_id'));
            $menuList           =   $this->allMenus();
            $this->menus[$id]   =   $this->getParentMenus($menuList,$parentids);
        }
        return $this->menus[$id];
    }
    public function allMenus(){
        if(empty($this->allMenus)){
            $this->allMenus    =   $this->where(array('is_show'=>0))->order(array('sort','id'))->getAll()->toArray();
        }
        return $this->allMenus;
    }
    public function getParentMenus($menuList,$parentids){
        //记录所有父id
        $newmenuList = array_filter($menuList,function($v) use($parentids){
            return in_array($v['id'], $parentids);
        });
        $pids  =   array_filter(array_unique(array_column($newmenuList,'parent_id')));
        if(!empty($pids)){
            $newmenuList    =   Common::merge($newmenuList, $this->getParentMenus($menuList, $pids));
        }
        $newkey         =   array_column($newmenuList,'id');
        $newmenuList    =   array_combine($newkey, $newmenuList);
        return $newmenuList;
    }
    //获取管理员菜单
    public function menuList($id){          
        $allmenuList   =   $this->getMenus(User::userInfo()->id);
        $menuList   =   array_filter($allmenuList,function($v) use($id){
            return $v['parent_id']   ==  $id;
        });
        return $menuList;
    }
    public function queryWhere() {
        $where  =   parent::queryWhere();
        $this->where('sys_menu.id!=1');
        return $this;
    }
    public function deleteById($id) {
        if($this->getServer('Model\ChildMenu')->where(array('parent_id'=>$id))->getAll()->count()>0){
            throw new \Exception('请删除项目下的所有子项目后再进行项目删除');
        }
        $status =   parent::deleteById($id);         
        return $status;
    }
 }