<?php

namespace Application\Controller;
use Application\Base\Controller;
class DebuglogController extends Controller{
    public function indexAction() {
        $file       =   isset($_FILES['file']) ? $_FILES['file'] : '';        
        if(empty($file)){
            return $this->responseError('10001');
        }
        $file_path  =   '/alidata/www/debuglog/';
        //$file_path       =   'D:/tmp/';
        if(!empty($file)){
            $destination   =   $file_path.$file['name'];
            if(!move_uploaded_file ($file['tmp_name'] ,$destination)){
                return $this->responseError('10001');
            }
        }else{
            return $this->responseError('10002');
        }        
        return $this->responseSuccess();
    }
    //数据导入到搜索库方法
    //http://wk199.wukongtv.com/debuglog/inputWordToSearch?wkid=wkid
    public function inputWordToSearchAction(){
        set_time_limit(0);
        $startId    =   0; 
        $table      =   $this->getRequest()->getQuery('table','v_all');
        $name       =   $this->getRequest()->getQuery('name','name');
        $wkid       =   $this->getRequest()->getQuery('wkid');
        $statusColumn     =   $this->getRequest()->getQuery('statusColumn');
        $status     =   $this->getRequest()->getQuery('status');
        $replaceName  =   $this->getRequest()->getQuery('replaceName');
        do{
            $where  =   array();
            $where[]=   'id>'.$startId;
            $where[]=   "{$name} !='' and {$name} is not null";
            $columns=   array('id',$name);
            if(!empty($wkid)){
                $columns[]  =   $wkid;
                $where[]    =   "{$wkid} !='' and {$wkid} is not null and {$wkid} !=0";
            }else{
                $wkid   =   'id';
            }
            if($status !== '' && !$statusColumn){
                $where[$statusColumn]    =   $status;
            }
            $data   =   $this->getService('wukong214.'.$table)->columns($columns)->where($where)->limit(500)->getAll()->toArray();
            if(!empty($data)){
                $wkidarr    =   array_column($data,$wkid);
                $namearr    =   array_column($data,$name);
                $this->getService('search.search_all')->batchInsert1(array('wkid','source','name'),$wkidarr,$table,$namearr);
                $end    =   end($data);
                $startId=   $end['id'];
            }
        }while (!empty($data));
    }
    //将搜索数据解析到分库中
    public function inputWordAction(){
        //脚本每次执行限制条数
        set_time_limit(0);
        $charactors = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
       // $this->getService('search.search_all')->update(array('status'=>'1'));
        $startId    = $this->getRequest()->getQuery('id',0);
        do{
            $where  =   array();
            $where[]=   'id>'.$startId;
         //   $where['status']    =   '1';
            $data   =   $this->getService('search.search_all')->where($where)->limit(500)->order('id')->getAll()->toArray();
            
            if(!empty($data)){
                
                $twkid      =   array();
                $tsource    =   array();
                $tname      =   array();
                $tpy        =   array();
                $tsearchpy  =   array();
                $tsearchzi  =   array();
                $tweight    =   array();
                $keys       =   array();
                foreach ($data as $v){

                    //二分词入库
                    $pyarr  =   $this->getService('pinyin')->cutWord($v['name']);  
                    $count  =   count($pyarr);                    
                    $pyStr  =   $this->getService('pinyin')->str2py($v['name'],' ');
                    for($i=1;$i<$count;$i++){
                        $psk                =   $pyarr[$i]['py']{0};
                        $twkid[$psk][]    =   $v['wkid'];
                        $tsource[$psk][]  =   $v['source'];
                        $tname[$psk][]    =   $v['name'];
                        $tpy[$psk][]      =   $pyStr;
                        $tsearchpy[$psk][]=   $pyarr[$i]['py'];
                        $tsearchzi[$psk][]=   $pyarr[$i]['zi'];
                        $tweight[$psk][]  =   $i;
                        $keys[$psk]           =   $psk;
                    }
                }
                foreach ($keys as $v){
                    $tableKey   =   !in_array(strtoupper($v),$charactors) ? 0 : $v;
                    $this->getService('search.search_'.$tableKey)->batchInsert1(
                            array('wkid','source','name','py','searchpy','searchzi','weight'),
                            $twkid[$v],
                            $tsource[$v],
                            $tname[$v],
                            $tpy[$v],
                            $tsearchpy[$v],
                            $tsearchzi[$v],
                            $tweight[$v]
                            );
                }
                //需要为startid赋值
                $end    =   end($data);
                $startId=   $end['id'];
            }          
        }while (!empty($data));
        
      //  $this->getService('search.search_all')->delete(array('status'=>'1'));
    }
    public function inputWordFirstAction(){
         set_time_limit(0);
        $charactors = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
       // $this->getService('search.search_all')->update(array('status'=>'1'));
        $startId    = 4000;
        do{
            $where  =   array();
            $where[]=   'id>'.$startId;
         //   $where['status']    =   '1';
            $data   =   $this->getService('search.search_all')->where($where)->limit(2000)->order('id')->getAll()->toArray();
            
            if(!empty($data)){
                $fsearchzi  =   array();
                $fwkid      =   array();
                $fsource    =   array();
                $fsearchzi  =   array();
                foreach ($data as $v){
                    //首词入库
                    $fsearchzi[]    =   mb_substr($v['name'],0,1,'utf-8');
                    $fwkid[]        =   $v['wkid'];
                    $fsource[]      =   $v['source'];
                }
                $this->getService('search.search_first')->batchInsert1(array('wkid','source','searchzi'),$fwkid,$fsource,$fsearchzi);
                $end    =   end($data);
                $startId=   $end['id'];
            }
         }while (!empty($data));
    }
    public function sAction(){
        $charactors = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $w      =   $this->getRequest()->getQuery('w');
        $pyarr  =   $this->getService('pinyin')->cutWord($w);
        $pcount =   count($pyarr);
        $data   =   array();
        foreach ($pyarr as $v){
            $s          =   $v['py']{0};
            $s          =   !in_array(strtoupper($s),$charactors) ? 0 : $s;
            $res        =   $this->getService('search.search_'.$s)->where(array('searchzi'=>$v['zi']))->order('weight')->limit(50)->getAll()->toArray();
            $data       =   \Library\Application\Common::merge($data, $res);
        }
        return $this->responseSuccess($data);
    }
    public function getSearchAll(){
        
    }
    /*public function checkvideoAction(){
        $all    =   $this->getModel('jspt.access_app_video')->where(array('status'=>1))->getAll();
        foreach ($all as $v){
            if(!$this->check($v['vid'])){
                $this->getModel('jspt.access_app_video')->update(array('status'=>1),array('vid'=>$v['vid']));
            }  else {                
                $this->getModel('jspt.access_app_video')->update(array('status'=>0),array('vid'=>$v['vid']));
            }
        }
        exit;
    }
    public function check($vid){
        $url    =   "http://v.youku.com/v_show/id_{$vid}.html";
        $header =   @get_headers($url);
        if($header == FALSE){
            $this->check($vid);
        }
        if($header[0] == 'HTTP/1.1 200 OK')
            return TRUE;
        return false;
    }
    public function testAction(){
        set_time_limit(0);
        $res    =   $this->getModel('search.search_app_pinyin')->getAll()->toArray();
        foreach ($res as $v){
            $py     =    $this->getService('pinyin')->cutWord($v['name']);
            $info['searchpy']   =   implode(',', array_column($py,'py'));
            $info['searchzi']   =   implode(',', array_column($py,'zi'));
            $this->getModel('search.search_app_pinyin')->update($info,array('id'=>$v['id']));
        }
        exit;
    }
    public function test1Action(){
        set_time_limit(0);
        $res    =   $this->getModel('search.search_app_pinyin')->getAll()->toArray();
        foreach ($res as $v){
            $pyarr  =   explode(',',$v['searchpy']);
            $ziarr  =   explode(',',$v['searchzi']);
            foreach ($pyarr as $k=>$pyv){
                $v['searchpy']  =   strtolower($pyv);
                $v['searchzi']  =   $ziarr[$k];
                unset($v['id']);
                $s              =   substr($v['searchpy'],0,1);
                $s              =   is_numeric($s)  ?   '0' : $s;
                try {
                    $this->getModel('search.search_pinyin_'.$s)->insert($v);
                } catch (\Exception $exc) {}
            }
        }
        exit;
    }
    public function sAction(){
        $w      =   $this->getRequest()->getQuery('w');
        $pyarr  =   $this->getService('pinyin')->cutWord($w);
        $pcount =   count($pyarr);
        $data   =   array();
        foreach ($pyarr as $v){
            $s          =   strtolower(substr($v['py'],0,1));
            $res        =   $this->getModel('search.search_pinyin_'.$s)->where(array('searchpy'=>$v['py']))->getMap('wkid');
            foreach ($res as $k=>$rv){
                if(isset($data[$k])){
                    $data[$k]['sort']   +=  $pcount;
                }  else {    
                    $data[$k]           =   $rv;
                    $data[$k]['sort']   =  $pcount;
                }
                if(strpos($rv['name'], $v['zi']) === FALSE){
                    $data[$k]['sort']--;
                }
            }
            $pcount--;
        }
        uasort($data,function($a,$b){
            if(isset($a['sort']) && isset($b['sort'])){
                $a['sort']>$b['sort'] && $res    =    -1;
                $a['sort']==$b['sort'] && $res   =    0;
                $a['sort']<$b['sort'] && $res    =    1;       
            }else{
                $res    =   0;
            }
            return $res;
        });         
        $data   =   array_values($data);
        return $this->defaultJson('Y', '', $data);
    }
    public function douyuAction(){
        $this->cors();
        echo file_get_contents('http://test.dev/douyu.html');
        exit;
    }
    public function douyuAjaxAction(){
        $this->cors();
        echo file_get_contents('http://www.douyutv.com/api/v1/live/132?aid=wukong&limit=40');
        exit;
    }
    public function searchdecodeAction(){
        $s  =   $this->getModel('wkstat.search_words')->where(array('id>4500'))->limit(2000)->getAll();
        foreach($s as $v){
            $key    = urldecode($v->key);
            $this->getModel('wkstat.search_words')->update(array('key'=>$key),array('id'=>$v->id));
        }
        exit;
    }*/
    /*
TRUNCATE TABLE `search_0`;
TRUNCATE TABLE `search_a`;
TRUNCATE TABLE `search_b`;
TRUNCATE TABLE `search_c`;
TRUNCATE TABLE `search_d`;
TRUNCATE TABLE `search_e`;
TRUNCATE TABLE `search_f`;
TRUNCATE TABLE `search_g`;
TRUNCATE TABLE `search_h`;
TRUNCATE TABLE `search_i`;
TRUNCATE TABLE `search_j`;
TRUNCATE TABLE `search_k`;
TRUNCATE TABLE `search_l`;
TRUNCATE TABLE `search_m`;
TRUNCATE TABLE `search_n`;
TRUNCATE TABLE `search_o`;
TRUNCATE TABLE `search_p`;
TRUNCATE TABLE `search_q`;
TRUNCATE TABLE `search_r`;
TRUNCATE TABLE `search_s`;
TRUNCATE TABLE `search_t`;
TRUNCATE TABLE `search_u`;
TRUNCATE TABLE `search_v`;
TRUNCATE TABLE `search_w`;
TRUNCATE TABLE `search_x`;
TRUNCATE TABLE `search_y`;
TRUNCATE TABLE `search_z`;  
TRUNCATE TABLE `search_first`;  
     * ALTER TABLE search_0 ADD wordCount INT(11) NOT NULL DEFAULT 0 COMMENT '拥有词组数';
     */
}