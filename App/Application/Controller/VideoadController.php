<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
use Library\Application\Common;
use Library\Application\Parameters;
class VideoadController extends PublicController
{
    public function init() {
        parent::init();
        /*if($this->selfTable()->table != 'fixed_ad'){
            $map        =   array_column($this->selfModel('v_type')->columns(array('key'=>new \Library\Db\Sql\Expression('concat(wktype,"_",wksubtype)'),
                                                                      'val'=>new \Library\Db\Sql\Expression('concat(wktype,"_",wksubtype)')))->getAll()->toArray(),'val','key');
            $param      =   $this->tableConfig()->getTableConfig()->columnList['position']['paramObj']->toArray();
            $this->tableConfig()->getTableConfig()->columnList['position']['paramObj']       =   new Parameters(Common::merge($param,$map)); 
            $this->tableConfig()->getColumnParam('position')->map;
        }*/
    }
}