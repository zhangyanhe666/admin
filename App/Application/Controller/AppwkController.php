<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;
use Application\Base\PublicController;
class AppwkController extends PublicController
{
    public function synchronousAction(){
        $source     =   $this->getRequest()->getQuery('source');
        $baoming    =   $this->getRequest()->getQuery('baoming');
        $item       =   $this->getServer($source)->where(array('baoming'=>$baoming))->getRow();
        if(empty($item)){
            return $this->responseError('数据不存在');
        }else{
            $item->app_id   =   $item->appid;
            return $this->responseSuccess($item);
        }
    }
    public function checkRepateAction(){
        $fieldName  =   $this->getRequest()->getQuery('fieldName');
        $val        =   $this->getRequest()->getQuery('val');
        $count      =   $this->selfTable()->where(array($fieldName=>$val))->count();
        if($count == 0){
             return $this->responseError('没有数据');
        }else{
            return $this->responseSuccess();
        }
    }
    public function updateSortAction(){
        $fieldName  =   $this->getRequest()->getQuery('fieldName');
        $val        =   $this->getRequest()->getQuery('val');
        $this->selfTable()->update(array($fieldName=>new \Library\Db\Sql\Predicate\Expression($fieldName.'+1')),array($fieldName.'>="'.$val.'"'));
        return $this->responseSuccess();
    }
}
