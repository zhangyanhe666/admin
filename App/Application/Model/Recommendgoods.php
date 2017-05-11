<?php
namespace Application\Model;
use Application\Model\SysModel;
class Recommendgoods extends SysModel
{
        public function init() {
            $this->setAdapter('jspt');
            $this->setTable('recommend_goods');
            parent::init();
        }
        public function add($info) {
            $id     =   parent::add($info);
            if(!empty($info['buy_url'])){
                $sinfo['url']      =   $info['buy_url'];
                $sinfo['urlmd5']   =   md5($info['buy_url']);
                $this->getServer('jspt.shortconnection')->insert($sinfo);
            }
            return $id;
        }
        public function edit(){
            $id     =   parent::edit($info);
            if(!empty($info['buy_url'])){
                $sinfo['url']      =   $info['buy_url'];
                $sinfo['urlmd5']   =   md5($info['buy_url']);
                $this->getServer('jspt.shortconnection')->insert($sinfo);
            }
            return $id;
        }
        public function deleteById($id) {
            $buy_url=   $this->getItem($id)->buy_url; 
            $urlmd5 =   md5($buy_url);
            $this->getServer('jspt.shortconnection')->delete(array('urlmd5'=>$urlmd5));
            parent::deleteById($id);
        }
   
 }