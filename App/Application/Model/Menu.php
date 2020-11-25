<?php
namespace Application\Model;
class Menu extends Model
{

    public $tableName   =   'sys.sys_childmenu';

    public  $menu;
    public function setMenu($menu_id){
        $this->menu     =   $this->select(['id'=>$menu_id])->current();
        return $this;
    }

    /**
     * 获取菜单列表
     * @Author   zhangyanhe
     * @DateTime 2020-05-20
     * @return   [type]     [description]
     */
    public function getMenuList(){
        $list   =   $this->select()->toArray();
        $list   =   array_column($list,null, 'id');
        foreach ($list as $k=>&$value) {
            if(!isset($list[$value['parent_id']])){
                continue;
            }
            $list[$value['parent_id']]['childList'][]   =   $value;
            unset($list[$k]);
        }
        return $list;
    }

    public function getIndexList($defaultNum=10){
        $page   =   $this->getService('request')->getQuery('page',1);
        $num    =   $this->getService('request')->getQuery('num',$defaultNum);
        $items      =  $this->queryColumns()
                            // ->queryWhere()
                            ->queryOrder()
                            // ->joinTable()//->getSqlString();
                            ->paginator($page,$num);
        return $items;
    }

    // public function getIndexList(){
    //     $this->
    // }
}