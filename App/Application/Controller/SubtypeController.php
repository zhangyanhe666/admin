<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
class SubtypeController extends PublicController
{
    public function deleteAction() {
        $this->selfTable()->update(array('status'=>0),array('id'=>$this->getRequest()->getQuery('id')));
    }
}