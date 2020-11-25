<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Model;
use Application\Model\SysModel;
use Library\Application\Common;

class CantvHttp extends SysModel{
    //http://hezuo.cms.can-tv.cn/Home/Index/getCommonMovieList?topicID=46&pageNumber=1&pageSize=10&pageid=1&typeID=1&partners=wk10010
    //http://hezuo.cms.can-tv.cn/Home/Index/getMovieDetail?programSeriesId=116354
    //http://hezuo.cms.can-tv.cn/Home/Index/getCommonMenuList?typeID=9
    const HOST          =   'http://hezuo.cms.can-tv.cn/';
    const MOVIE_LIST    =   'Home/Index/getCommonMovieList?topicID=@topicID&pageNumber=@pageNumber&pageSize=@pageSize&pageid=1&typeID=@typeID&partners=wk10010';
    const MOVIE_DETAIL  =   'Home/Index/getMovieDetail?programSeriesId=@programSeriesId';
    const MENU_LIST     =   'Home/Index/getCommonMenuList?typeID=@typeID&channelid=20001';
    const LIVE_LIST     =   'v2/Channel/getallchannel';
    public $typeIds     =   array(
        array('id'=>'1','name'=>'电影'),
        array('id'=>'2','name'=>'电视剧'),
        array('id'=>'3','name'=>'动漫'),
        array('id'=>'4','name'=>'综艺'),
        array('id'=>'5','name'=>'纪录片'),
        array('id'=>'6','name'=>'教育'),
        array('id'=>'9','name'=>'禅文化'),
        array('id'=>'1009','name'=>'国际频道'),
    );
    public $msg=array();
    public function init() {}
    public function apiUrl($uri,$param=array()){
        $url    =   self::HOST.Common::replace_tag($uri, $param,'@');
        $this->msg[]    =   $url;
        $data   =   $this->getService('curl')->exec($url)->json(TRUE);
        return $data;
    }
    
    public function videoParentCategory(){
        return $this->typeIds;
    }
    
    public function videoCategory($typeId){
        $data    =   $this->apiUrl(self::MENU_LIST,array('typeID'=>$typeId));
        if(!isset($data['menuList']) || empty($data['menuList'])){
            throw  new \Exception('不存在menuList数据，请求typeid：'.$typeId);
        }
        return  $data['menuList'];
    }
    
    public function videoByCategory($topicID,$typeId,$pageNumber,$pageSize){
        $data    =   $this->apiUrl(self::MOVIE_LIST,array('topicID'=>$topicID,'pageNumber'=>$pageNumber,'pageSize'=>$pageSize,'typeID'=>$typeId));
        return  isset($data['programList']) ? $data['programList'] : array();
    }
    
    public function videoInfo($id){
        $data    =   $this->apiUrl(self::MOVIE_DETAIL,array('programSeriesId'=>$id));
        return  $data;
    }
    public function liveList(){
        $data    =   $this->apiUrl(self::LIVE_LIST);
        return  $data;
    }
}