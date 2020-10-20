<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller;
use Application\Base\PublicController;
use Application\Tool\Html;
class SpecialnewController extends PublicController
{
    public $specialDir  =   'zhuanti214';
    public $specialTpl  =   'special212';
    public function resetspecial(){
        $paramObj   =   $this->tableConfig()->getColumnParam('content');
        $this->specialDir   =   $paramObj->get('dir',  $this->specialDir);
        $this->specialTpl   =   $paramObj->get('tpl',  $this->specialTpl);
    }
    public function indexAction() {
        $this->resetspecial();
        $this->tableConfig()->getColumnList()->title['viewType']    =   'outLink';
        $this->tableConfig()->getColumnParam('title')->url  =   "http://static1.wukongtv.com/special/{$this->specialDir}/z_%s.html";
        $this->tableConfig()->getColumnParam('title')->param  =   array('id');
        parent::indexAction();
        Html::addOption('toindexspecial', '迁到首页精选',array('exec'=>0));
    }

    public function toindexspecialAction(){        
        $id         =   $this->getRequest()->getQuery('id');
        if(empty($id) || !($item    =   $this->selfTable()->getItem($id))){
             return $this->responseError('复制失败,联系管理员');
        }
      
        $data['name']       =   $item->title;
        $data['cover']      =   $item->cover;
        $data['tag']        =   $item->tag;
        $data['color']      =   $item->color;
        $data['switch']      =   0;
        $data['router']     =   30;
        $data['routerValue']=   "http://static1.wukongtv.com/special/{$this->specialDir}/z_{$id}.html";
        $data['sort']       =   $this->selfModel('index_chosen')->getColumn('max(sort)')+1;
        $this->selfModel('index_chosen')->insert($data);
       return $this->responseSuccess();
    }
    public function doAddAction() {
        parent::doAddAction();
        
        $this->resetspecial();
        $id     =   $this->selfTable()->getLastInsertValue();
        $webUrl                     =   "http://static1.wukongtv.com/special/{$this->specialDir}/z_{$id}.html";
        $searchWeb['title']         =   $this->getRequest()->getPost('title');
        $searchWeb['search_word']   =   $this->getRequest()->getPost('search_word');
        $searchWeb['cover']         =   $this->getRequest()->getPost('ccover');
        $searchWeb['webUrl']        =   $webUrl;
        $searchWeb['type']          =   $this->getRequest()->getPost('type');
        $searchWeb['special_id']    =   $id;
        if(!array_search('', $searchWeb)){
            $this->selfModel('search_web')->add($searchWeb);
        }
        $this->staticPage($id);
    }
    public function doEditAction() {
        parent::doEditAction();
        
        $this->resetspecial();
        $id     =   $this->getRequest()->getPost('id');
        $searchWeb['title']         =   $this->getRequest()->getPost('title');
        $searchWeb['search_word']   =   $this->getRequest()->getPost('search_word');
        $searchWeb['cover']         =   $this->getRequest()->getPost('ccover');
        $searchWeb['type']          =   $this->getRequest()->getPost('type');
        if(!array_search('', $searchWeb)){
            $this->selfModel('search_web')->update($searchWeb,array('special_id'=>$id));
        }
        $this->staticPage($id);        
    }
    private function staticPage($id){
        //获取静态页
        $special            =    $this->selfTable()->where(array('id'=>$id))->getRow();
        //分享内容
        $share['title']     =   $special['share_title'];
        $share['content']   =   $special['share_content'];
        $share['link']      =   empty($special['share_link']) ? "http://static1.wukongtv.com/special/{$this->specialDir}/z_{$id}.html" : $special['share_link'];
        $share['cover']     =   $special['share_icon'];  
        $share['ccover']     =   $special['ccover'];  
        //获取模板信息
        $this->viewData()->setVariable('special',$special);
        $this->viewData()->setVariable('share',json_encode($share));
        $this->viewData()->setVariable('tag',$special['tag']);
        $content    =   $this->getService('template')->template('lib/'.$this->specialTpl);
        //上传专题到指定服务器
        $path       =   $this->config()->filePath('Cache/Tmp/tmp'.rand(0, 100));
        $serverFile =   "special/{$this->specialDir}/z_{$id}.html";
        $remote_file=   "/alidata/www/static1.wukongtv.com/web_yaokong/".$serverFile;
        file_put_contents($path, $content);
        $server     =   $this->getService('sys.sys_ftp_config')->getItem(4);
        $this->getService('ftp')->connect($server)->put($remote_file ,$path,FTP_BINARY);
        //更新专题缓存
        $this->getService('Tool\Cdn')->updateCdn('http://static1.wukongtv.com/'.$serverFile);
    }
    public function getVidAction(){
        $source =   $this->getRequest()->getQuery('source');
        $id     =   $this->getRequest()->getQuery('id');
        $zhibo  =   array(
            'vsttp'=>'original',
            'aniu'=>'original',
            'douyu'=>'original',
            'dsj'=>'zhibo',
            'dsm'=>'zhibo',
            'cibn'=>'dianbo',
            'dsmdian'=>'dianbo',
            'mango'=>'dianbo',
            'mifeng'=>'dianbo',
            'moli'=>'dianbo',
            'qq'=>'dianbo',
            'vstdian'=>'dianbo',
            'youku'=>'dianbo',
            'iqiyi'=>'dianbo'
            );
        switch ($zhibo[$source]){
            case 'original':
                break;
            case 'zhibo':
                $id =   $this->selfModel('zhibo_m2')->where(array('wkid'=>$id,'src'=>$source))->getRow()->playurl;
                break;
            case 'dianbo':                
                switch ($source){
                    case 'iqiyi':
                        $res     =   $this->selfModel('v_all')->where(array('wkid'=>$id))->getRow();
                        $id      =   htmlspecialchars('#Intent;action=com.gitvdemo.video.action.ACTION_DETAIL;launchFlags=0x20;S.playInfo={"videoId":"'.$res->lizhi_vid.'","episodeId":"'.$res->lizhi_tvQId.'","chnId":"2"};end');
                        break;
                    default :
                        $column     =   str_replace('dian','',$source).'_vid';
                        $id =   $this->selfModel('v_all')->where(array('wkid'=>$id))->getRow()->{$column};
                }
                break;
        }
        $data['id'] =   $id;
        return $this->responseSuccess($data);
    }
    public function getMoviceAction(){
        $movicename     =   $this->getRequest()->getQuery('movicename');
        $moviceInfo     =   $this->selfModel('movie_critic')->where(array('name'=>$movicename))->getRow();
        if(!empty($moviceInfo)){
            return $this->responseSuccess($moviceInfo);
        }else{
            return $this->responseError();
        }
    }
}
