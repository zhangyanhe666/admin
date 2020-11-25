<?php
namespace Application\Model;
use Application\Model\SysModel;
use Library\Application\Common;
use Application\Tool\User;
use Application\Tool\Authority;
class ChildMenu extends Model
{
    public $childMenus  =   array();
    public $tableMap    =   array();
    public $tableName   =   'sys.sys_childmenu';

    public function getItem($id){
        if(!isset($this->tableMap[$id])){
            $this->tableMap[$id]   =   parent::getItem($id);
        }
        return $this->tableMap[$id];
    }
    //获取当前项目数据
    public function getMenu(){
        $menu_id    =   $this->getService('router')->getMenuId();
        return  $this->getTableGateway()->select(['id'=>$menu_id]);
    }
    //获取用户所有子菜单
    public function getChildMenus($id){
        $gid    =   $this->getService('Model\AdminUser')->getItem($id)->group_id;
        //判断超级管理员组
        if($this->getService('Model\AdminGroup')->isSuperAdmin($gid)){
            $cmenus =   $this->where(array('is_show'=>0))->order(array('sort','id'))->getAll()->toArray();
        }else{
            $cmenus =   $this->where(array('is_show'=>0,'b.gid'=>$gid))->join(array('b'=>'sys_group_map'),'sys_childmenu.id=b.menu_id',array())->order(array('sys_childmenu.sort','sys_childmenu.id'))->getAll()->toArray();
        }
        return $cmenus;
    }
    //获取指定父id的子菜单
    public function childMenuList($id){
        static $allmenuList    =   null;
        if(!$allmenuList){
            $allmenuList   =   $this->getChildMenus(User::userInfo()->id);
        }
        $menuList   =   array_filter($allmenuList,function($v) use($id){
            return $v['parent_id']   ==  $id;
        });
        return $menuList;
    }
    public function getMenuList($pid=0){
        $data   =   array();
        $menuList   =   $this->getService('Model\Menu')->menuList($pid);
        if(!empty($menuList)){
            foreach ($menuList as $v){
                $data[$v['name']]   = Common::merge($this->getMenuList($v['id']), $this->childMenu($v['id']));
            } 
        }
        return $data;
    }
    public function childMenu($id){
        $childMenu  = Common::arrayResetKey($this->childMenuList($id), 'name');
        $childMenu  = array_map(function($v){
            return array_flip(array_map(function($u) use($v){
                return $v['id'].'.'.$u;                
            }, Authority::$authority));
        }, $childMenu);
        return $childMenu;
    }
    //处理删除子项目
    public function delChildMenu($id,$tableList){
        if(!empty($tableList)){
            $where['parent_id']     =   $id;
            $where['table_name']    =   $tableList;
            $status     =   $this->delete($where);
            return $status;
        }
        return false;
    }
    //处理添加子项目
    public function addChildMenu($id,$tableList){
        if(!empty($tableList)){
            $column =   array('parent_id','name','table_name','action');
            $parentIdVal    =   array_fill(0,count($tableList),$id);
            $nameVal        =   array_map(function($v){
                return $this->getTableComment($v);
            },$tableList);
            $tableVal       =   $tableList;
            $actionVal      =   array_fill(0,count($tableList),'dispatch');
            $status         =   $this->batchInsert($column,array_map(null,$parentIdVal,$nameVal,$tableVal,$actionVal));
            return $status;
        }
        return false;
    }
    public function getTableComment($v){
        static $dblist =   array();
        if(strpos($v,'.') == FALSE){
            return '';
        }
        list($db,$table)    =   explode('.',$v);
        !isset($dblist[$db])  &&  $dblist[$db]    =   array_column($this->getService('Model\InformationSchema')->config($db)->getAllTables(),'TABLE_COMMENT','TABLE_NAME');
        $comment    =   isset($dblist[$db][$table]) && !empty($dblist[$db][$table]) ? $dblist[$db][$table] : $table;
        return $comment;
    }
    public function getMenuByParentId($parent_id){
        $menus  =   $this->where(array('parent_id'=>$parent_id))->group('table_name')->getAll()->toArray();
        return $menus;
    }
 }