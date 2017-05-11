<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
use Library\Dom\Query;
use Application\Exception\AccessappalbumException;
use Application\Tool\Html;
class AccessappalbumController extends PublicController
{
    public function indexAction() {
        parent::indexAction();
        Html::addOption('updateVideo','更新视频',array('exec'=>0));
    }
    public function updateVideoAction(){        
       $id         =   $this->getRequest()->getQuery('id');
       if(empty($id) || !($item    =   $this->selfTable()->getItem($id))){
            return $this->responseError('更新失败联系管理员');
       }
       if($item->pos < 10000){
           return $this->responseError('自定义专辑不能更新');
       }
       $url     =   'http://www.youku.com/playlist/rss/id/'.$item->pos;
       $xml     =   $this->getServer('curl')->exec($url)->xml();
       if($xml === false){           
           return $this->responseError('获取专辑视频失败');
       }
       $items    =   $xml->execute('item'); 
       if($items->count()>0){
           $vids    =   array();
           foreach ($items as $k=>$v){
                $match  =   array();
                $link   =   $v->getElementsByTagName('link')->item(0)->nodeValue;
                preg_match('/id_([\w|\=]*)_rss/',$link,$match);
                if(!isset($match[1]) || $this->getServer('jspt.access_app_video')->where(array('vid'=>$match[1],'album_id'=>$id))->getRow()){
                    continue;
                }
                $vid        =   $match[1];
                $video_name =   $v->getElementsByTagName('title')->item(0)->nodeValue;
                $desc       =   new Query($v->getElementsByTagName('description')->item(0)->nodeValue);
                $imgs       =   $desc->execute('img');
                $pic        =   $imgs[0]->getAttribute('src');
                $info       =   array();
                $info['vid']        =   $vid;
                $info['video_name'] =   $video_name;
                $info['album_id']   =   $id;
                $info['app_id']     =   $item->app_id;
                $info['pos']        =   $item->pos;
                $info['create_time']=   date('Y-m-d H:i:s');
                $info['status']     =   1;
                $info['pic']        =   $pic;
                $this->getServer('jspt.access_app_video')->insert($info);
           }           
           return $this->responseSuccess();
        }
        return $this->responseError('失败');
    }
    public function updateAllAction(){
        set_time_limit(0);
        $items    =   $this->selfTable()->where(array('status'=>0,'pos > 10000'))->getAll();
        foreach ($items as $k=>$v){
            $this->updateVideo($v);
        }
        exit;
    }
    public function updateVideo($item){
       if($item->pos < 10000){
           $this->getServer('Model\Log')->write('自定义专辑不能更新');
           return ;
       }
       
           $this->getServer('Model\Log')->write('专辑:'.$item->pos);
       try {
           $YvideoList  =   $this->getVideoList($item->pos);
       } catch (AccessappalbumException $exc) {
           $this->getServer('Model\Log')->write('专辑获取错误：'.$exc->getMessage());
           return ;
       }

       $WvideoList  =   $this->selfModel('access_app_video')->where(array('album_id'=>$item->id))->getAll()->toArray();
       $Wkeys       =   array_column($WvideoList,'vid');
       $WvideoList  =   array_combine($Wkeys, $WvideoList);
       $ydiff       =   array_diff_key($YvideoList, $WvideoList);
       $wdiff       =   array_diff_key($WvideoList, $YvideoList);
       if(!empty($ydiff)){
           foreach ($ydiff as $v){
               $v['album_id']   =   $item->id;
               $v['app_id']     =   $item->app_id;
               $v['pos']        =   $item->pos;
               $v['create_time']=   date('Y-m-d H:i:s');
               $v['status']     =   0;
               $this->getServer('jspt.access_app_video')->insert($v);
           }
           $this->getServer('Model\Log')->write('新增视频'.count($ydiff).'个视频'.var_export($ydiff,TRUE));
       }
       
       if(!empty($wdiff)){
           $ids     =   array_keys($wdiff);
           $updateNum   =   $this->selfModel('access_app_video')->update(array('status'=>2),array(new \Library\Db\Sql\Predicate\In('vid', $ids)));
           $this->getServer('Model\Log')->write('欲删除'.$updateNum.'个视频：'.var_export($ids,TRUE));
       }
    }
    public function getVideoList($pos){
        $list   =   array();
        $url     =   'http://www.youku.com/playlist/rss/id/'.$pos;
        $xml     =   $this->getServer('curl')->exec($url)->xml();
        if($xml === false){
            throw new \AccessappalbumException('获取专辑视频失败pos:'.$pos);
        }
        $items    =   $xml->execute('item'); 
        if($items->count()>0){
            $vids    =   array();
            foreach ($items as $k=>$v){
                 $match  =   array();
                 $link   =   $v->getElementsByTagName('link')->item(0)->nodeValue;
                 preg_match('/id_([\w|\=]*)_rss/',$link,$match);
                 if(!isset($match[1])){
                     continue;
                 }
                 $desc       =   new Query($v->getElementsByTagName('description')->item(0)->nodeValue);
                 $imgs       =   $desc->execute('img');
                 $info       =   array();
                 $info['vid']        =   $match[1];
                 $info['video_name'] =   $v->getElementsByTagName('title')->item(0)->nodeValue;
                 $info['pic']        =   $imgs[0]->getAttribute('src');
                 $list[$match[1]]    =   $info;
            }           
            return $list;
        }else{           
            throw new \AccessappalbumException('专辑'.$pos.'无数据');
        }
    }
}