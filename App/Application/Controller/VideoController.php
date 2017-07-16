<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
use Application\Tool\Html;
class VideoController extends PublicController
{
    public $host    =   'http://jp.xieav.com';
    public $wyjcuri =   '/one/wyjc/index%s.shtml';
    public $uri     =   '/one/LLP/index%s.shtml';
    public function indexAction() {
        //设定工具栏
        Html::addOption('edit','编辑');
        Html::addOption('copy','复制');
        Html::addOption('delete','删除',array('onclick'=>  'admin.deleteItem(__id);'));
        if('wukong214'  == $this->selfTable()->dbKey()){
            Html::addOption('transfer','迁移到线上',array('exec'=>0));
        }          
        Html::toolDown();
        Html::toolUpload();
        Html::addTool('add','添加',array('href'=>  $this->router()->url(array('action'=>'add'),array(),true)));
        Html::addTool('tableconfig','表结构快速通道',array(
            'href'=>  $this->router()->url(array('control'=>'tableConfig','action'=>'edit'),array('menuId'=>$this->router()->getMenuId())),
            'target'=>'_blank'
        ));
        
        Html::addTool('getIndexHtml','更新第一页',array('href'=>  $this->router()->url(array('action'=>'getIndexHtml'),array(),true),'target'=>'_blank'));
        Html::addTool('updateVideo','更新视频',array('href'=>  $this->router()->url(array('action'=>'updateVideo'),array('page'=>0),true),'target'=>'_blank'));
        if('on'  == $this->getRequest()->getQuery('custom')){
            Html::addTool('index','完成自定义');
        }  else {            
            Html::addTool('custom','自定义显示',array(
                'href'=>  $this->router()->url(array(),array('custom'=>'on'))
            ));
        }

        if(!empty($this->getServer('Model\ChildMenu')->getMenu()->mem_url)){
             Html::addTool('clearCache','清理缓存',array('exec'=>0));
        }

        $this->viewData()->setVariable('items', $this->selfTable()->getIndexList())//获取分页列表数据
             ->setVariable('columnSwitch', $this->getServer('Model\Custom')->getMeans());//开关
             //->addTpl('lib/list');
    }

    public function dom($url){
        $html   =   $this->getServer('curl')->exec($url)->result();
        $html   =   str_replace('gb2312', 'utf-8', $html);
        $html   =   str_replace('<b>', '', $html);
        $html   =   iconv('GB2312', 'UTF-8//IGNORE', $html);
        $dom    =   new \DOMDocument('1.0','UTF-8');
        @$dom->loadHTML($html);
        return $dom;
    }
    public function getIndexHtmlAction(){
        set_time_limit(0);
        $url    =   $this->host.'/one/LLP/index.shtml';
        $list   =   $this->readList($url); 
        $this->getServer('test.video')->batchInsert(['name','icon','s','playurl','unique_key'],$list);
    }
    public function updateVideoAction(){
        set_time_limit(0);
        $page   =   $this->getRequest()->getQuery('page',2);
        if($page < 2 || $page > 1200){
           exit; 
        }
        $urls   =   $this->getUrlPath($page, $page);  
        foreach ($urls as $url){
            $list   =   $this->readList($url);
            $this->getServer('test.video')->batchInsert(['name','icon','s','playurl','unique_key'],$list);
        }
    }
    public function readList($url){
        $list   =   [];

        $dom    =   $this->dom($url);
        $divs   =   $dom->getElementsByTagName('div');

        foreach ($divs as $div){
            if($div->getAttribute('class') == 'movie-chrList'){
                foreach ($div->getElementsByTagName('li') as $li){
                    $uri    =   $li->getElementsByTagName('a')->item(0)->getAttribute('href');
                    $icon   =   $li->getElementsByTagName('img')->item(0)->getAttribute('src');
                    $name   =   $li->getElementsByTagName('a')->item(1)->nodeValue;
                    $status =   $li->getElementsByTagName('abbr')->item(0)->nodeValue;
                    $playurl=   $this->readPlayurl($this->host.$uri);
                    if(empty($playurl)){
                        continue;
                    }
                    $item   =   [];
                    $item['name']   =   $name;
                    $item['icon']   =   $icon;
                    $item['s']      =   $status;
                    $item['playurl']   =   $playurl;
                    $item['unique_key']   =   $uri;
                    $list[] =   $item;
                }
            }
        }
        return $list;
    }
    public function readPlayurl($url){
        $dom    =   $this->dom($url);
        $divs   =   $dom->getElementsByTagName('div');
        $playurl    =   '';
        foreach ($divs as $div){
            if($div->getAttribute('class') == 'playurl'){
                if($div->getElementsByTagName('span')->item(0)->getAttribute('class') == 'xfplay'){
                    $playinfo   =   $this->host.$div->getElementsByTagName('a')->item(1)->getAttribute('href');
                    $dom    =   $this->dom($playinfo);
                    $script =   $dom->getElementsByTagName('script')->item(8)->getAttribute('src');
                    $jsuri  =   $this->host.$script;
                    $jsstr  =   $this->getServer('curl')->exec($jsuri)->result();
                    preg_match('/\$xfplay(.*)\$xfplay/',$jsstr,$match);
                    if(!empty($match)){
                        $playurl    =  'xfplay'.$match[1];
                    }
                    break;
                }    
            }            
        }
        return $playurl;
    }
    public function getUrlPath($start,$end){
        $urls   =   [];
        $url    =   $this->host.$this->uri;
        for($i=$start;$i<=$end;$i++){
            $urls[] =   vsprintf($url, [$i]);
        }
        return $urls;
    }
    
}