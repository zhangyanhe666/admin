<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
class CetusplayappController extends PublicController
{
    
    public function doAddAction() {
        parent::doAddAction();
        $channel    =   explode(',',$this->getRequest()->getPost('channel','default,cyx'));
        $baoming    =   $this->getRequest()->getPost('baoming');
        $download_url    =   $this->getRequest()->getPost('download_url');
        $this->selfModel('appstore_source')->batchInsert1(['baoming','download_url','sort','switch','channel'],
                $baoming,$download_url,0,1,$channel
                );
    }
}