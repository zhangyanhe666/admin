<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Controller;
use Application\Base\Controller;
use Library\Application\Common;
class SearchController extends Controller{
    
    public function onDispatch() {
        $date   =   date('Y-m-d H:i:s');
        echo "脚本{$this->router()->getAction()}开始执行:{$date}\n";
        $startTime  =   microtime(TRUE);
        parent::onDispatch();
        $endTime  =   microtime(TRUE);
        $allTime  =   $endTime-$startTime;
        echo "脚本{$this->router()->getAction()}执行结束\n执行总时长：{$allTime}s\n";
        exit;
    }


    public function updateSearchWordAction(){
        set_time_limit(0);
        $id     =   0;
        $data   =   $this->getServer('wukong.v_all')->columns(array('id','name'))
                    ->where(array("search_word = '' and name !=''"))->order('id')->limit(5000)->getAll();
        $count  =   $data->count();
        if($count>0){
            foreach ($data as $v){
                $search_wordarr =   $this->getServer('pinyin')->strToPy($v->name); 
                $search_word    =   implode('', array_column($search_wordarr,'zi'));
                $this->getServer('wukong.v_all')->update(array('search_word'=>$search_word),array('id'=>$v->id));
            }
        }
        echo "数据总更新条数{$count}\n";
    }
    public function updateLiveSearchWordAction(){
        set_time_limit(0);
        $id     =   0;
        $data   =   $this->getServer('wukong.zhibo_m2')->columns(array('id','name'))
                    ->where(array("search_word = '' and name !=''"))->order('id')->limit(3000)->getAll();
        $count  =   $data->count();
        if($count>0){
            foreach ($data as $v){
                $search_wordarr =   $this->getServer('pinyin')->strToPy($v->name); 
                $search_word    =   implode('', array_column($search_wordarr,'zi'));
                $this->getServer('wukong.zhibo_m2')->update(array('search_word'=>$search_word),array('id'=>$v->id));
            }
        }
        echo "数据总更新条数{$count}\n";
    }
  /*  const METCH_WORDS   =   8;
    const METCH_DEGREE  =   0.5;
    public function testAction(){
        echo intval(14155053954/1024/1024);exit;
        $data   =   $this->getServer('search.search_all')->where(array('id'=>array('44184','92199')))->getAll()->toArray();
        var_dump($data);exit;
        $a  =   12345678900001111;
        echo $a;exit;
            $pyarr  =   $this->getServer('pinyin')->strToPy('山东卫视“重阳·九重礼”晚会'); 
            $gwords     =   $this->groupWords($pyarr);
            var_dump($gwords);exit;
            var_dump(count($gwords));exit;
    }
    //分词脚本
    public function indexAction() {
        set_time_limit(0);
        $date   =   date('Y-m-d H:i:s');
        echo "脚本开始执行:{$date}\n";
        $startTime  =   microtime(TRUE);
        $limit  =   $this->getRequest()->getQuery('len',3000);
        $sign   =   $this->getRequest()->getQuery('sign',1);
        $oldsign   =   $this->getRequest()->getQuery('oldsign',0);
        if($limit > 10000){
            echo '单次数据更新不能超过3000条';exit;
        }
        //设置要更新的数据
        $this->setUpdate($sign, $oldsign, $limit);
        
        $data   =   $this->getDate($sign, $limit);
        if(!empty($data)){
            $searchData         =   array();
            $searchFirstData    =   array();
            foreach ($data as $v){
                $pyarr  =   $this->getServer('pinyin')->strToPy($v['name']);
                if(!empty($pyarr)){
                    
                    $gwords     =   $this->groupWords($pyarr);
                    $wordCount  =   count($gwords);
                    if($wordCount<3){
                        $firstTmp   =   array();
                        $firstTmp['wkid']       =   $v['wkid'];
                        $firstTmp['source']     =   $v['source'];
                        $firstTmp['name']       =   $v['name'];
                        $firstTmp['searchzi']   =   $pyarr[0]['zi'];            
                        $firstTmp['wordCount']  =   $wordCount;            
                        $searchFirstData[]      =   $firstTmp;
                    }
                    $tmp        =   array();
                    $allpy  =   implode(' ', array_column($pyarr,'py'));
                    foreach ($gwords as $gk=>$gv){
                        $tmp['wkid']        =   $v['wkid'];
                        $tmp['source']      =   $v['source'];
                        $tmp['name']        =   $v['name'];
                        $tmp['py']          =   $allpy;
                        $tmp['searchpy']    =   $gv['py'];
                        $tmp['searchzi']    =   $gv['zi'];
                        $tmp['weight']      =   $gk;
                        $tmp['wordCount']   =   $wordCount;
                        $psk                =   $gv['py']{0};
                        $searchData[$psk][] =   $tmp;
                    }
                }
            }
            if(!empty($searchData)){       
                $columns    =   array_keys(current(current($searchData)));
                foreach ($searchData as $k=>$sv){
                    $kk     =   is_numeric($k) ? 0 : strtolower($k);
                    $this->getServer('search.search_'.$kk)->batchInsert($columns,$sv);
                }
            }
            if(!empty($searchFirstData)){       
                $columns    =   array_keys(current($searchFirstData));
                $this->getServer('search.search_first')->batchInsert($columns,$searchFirstData);                
            }
            //设置要更新的数据完成更新
            $this->setUpdate(-1, $sign, $limit);
        }
        echo "脚本执行结束\n";
        $endTime  =   microtime(TRUE);
        $allTime  =   $endTime-$startTime;
        echo "执行总时长：{$allTime}\n";exit;
    }
    //数据导入到搜索库脚本
    //http://wk199.wukongtv.com/debuglog/inputWordToSearch?wkid=wkid
    public function insertSearchAction(){
        set_time_limit(0);
        $date   =   date('Y-m-d H:i:s');
        echo "insert脚本开始执行:{$date}\n";
        $startTime  =   microtime(TRUE);
        $startId    =   0; 
        $table      =   $this->getRequest()->getQuery('table'); //数据库名称
        $name       =   $this->getRequest()->getQuery('name'); //被搜索的字段
        $wkid       =   $this->getRequest()->getQuery('wkid'); //悟空id字段
        if(empty($table) || empty($name) || empty($wkid)){
            echo '输入信息不准确';exit;
        }
        do{
            $where  =   array();
            $where[]=   'id>'.$startId;
            $where[]=   "{$name} !='' and {$name} is not null";
            $columns=   array('id',$wkid,$name);
            $where[]    =   "{$wkid} !='' and {$wkid} is not null and {$wkid} !=0";
            $data   =   $this->getServer('wukong214.'.$table)->columns($columns)->where($where)->limit(500)->getAll()->toArray();
            if(!empty($data)){
                $wkidarr    =   array_column($data,$wkid);
                $namearr    =   array_column($data,$name);
                $this->getServer('search.search_all')->batchInsert1(array('wkid','source','name'),$wkidarr,$table,$namearr);
                $end    =   end($data);
                $startId=   $end['id'];
            }
        }while (!empty($data));
        echo "insert脚本执行结束\n";
        $endTime  =   microtime(TRUE);
        $allTime  =   $endTime-$startTime;
        echo "执行总时长：{$allTime}\n";exit;
    }
   
    
    
    
    //搜索功能
    public function sAction(){
        header("Content-Type: text/html; charset=UTF-8");
        $data   =   array();
        $startTime  =   microtime(TRUE);
        
        
        //搜索词
        $w      =   $this->getRequest()->getQuery('w');
        //精准度
        $degree =   $this->getRequest()->getQuery('degree',  self::METCH_DEGREE);
        $degree =   $degree > 0 && $degree < 1 ? $degree : self::METCH_DEGREE;
        //翻译
        $pyarr  =   $this->getServer('pinyin')->strToPy($w);
        if(!empty($pyarr)){
            if(count($pyarr) == 1){
                $data     =    $this->getServer('search.search_first')
                        ->where(['searchzi'=>$pyarr[0]['zi']])->order('wordCount')->limit(50)->getAll()->toArray();
            }else{                
                $res      =   $this->searchData($pyarr);
//                    $translation=   $this->getServer('search.search_translation')->order('strlen')->getAll()->toArray();
//                    if(!empty($translation)){
//                        foreach ($translation as $v){
//                            $w  = str_replace($v['from_name'], $v['to_name'], $w);
//                        }
//                        $pyarr1         =   $this->getServer('pinyin')->strToPy($w);
//                        $gwords1        =   array_column($this->groupWords($pyarr1),'py');
//                        $searchMetch1   =   $this->searchMetch(count($gwords1), $degree);
//                        $data2          =   $this->searchData(array_diff($gwords1,$gwords),$searchMetch1);
//                        $data2          =   array_map(function($v){
//                            $v['weight']    =   $v['weight']-1;
//                            return $v;
//                        },$data2);
//                        $data1          =   Common::merge($data1, $data2);
//                    }
                foreach ($res as $dv){                    
                    $dkeys          =   $dv['source'].$dv['wkid'];
                    if(isset($data[$dkeys])){
                        $data[$dkeys]['metchNum']   +=   1;
                        $data[$dkeys]['weight']     =   $data[$dkeys]['weight'] > $dv['weight'] ? $dv['weight'] : $data[$dkeys]['weight'];
                    }else{
                        $data[$dkeys]               =   $dv;
                        $data[$dkeys]['metchNum']   =   1;
                    }
                }
                if($searchMetch['metchWNum'] >1){
                    $data   =   array_filter($data,function($v) use($searchMetch){
                        return $searchMetch['metchWNum'] <= $v['metchNum'];
                    });
                }
                $data   =   $this->ssort($data,$w);
            }
        }
        $endTime  =   microtime(TRUE);
        $allTime  =   $endTime-$startTime;
        $this->viewData()->setVariable('allTime',$allTime);
        $this->viewData()->setVariable('data',$data);
        
    }
    
    public function searchMetch($wordCount,$degree){
        $metchWNum  =   intval(ceil($wordCount*$degree))-1;//匹配次数
        $wMaxNum    =   intval(ceil($wordCount/$degree));    //最大词数
        $wMaxNum    =   ($wMaxNum > self::METCH_WORDS) ? self::METCH_WORDS :  $wMaxNum;//最大词数
        return array('metchWNum'=>$metchWNum,'wMaxNum'=>$wMaxNum);
    }

    public function searchData($pyarr){
        $where      =   array();
        $data       =   array();
        $words      =   $this->groupWords($pyarr);
        $min        =   count($words)-2;
        $max        =   count($words)+2;
        $where[]    =   "wordCount >= {$min} and wordCount <= {$max}";
        foreach ($words as $k=>$v){
            var_dump($v);exit;
            $kk     =   is_numeric($v{0}) ? 0 : strtolower($v{0});
            $where['searchpy']  =   $v;
            $data1  =   $this->getServer('search.search_'.$kk)->where($where)->getAll()->toArray();
            $data   =   Common::merge($data, $data1);
//            foreach ($data1 as $dv){
//                $dkeys      =   $dv['source'].$dv['wkid'];
//                if(isset($data[$dkeys])){
//                    $data[$dkeys]['metchNum']   +=   1;
//                    $data[$dkeys]['weight']     =   $data[$dkeys]['weight'] > $dv['weight'] ? $dv['weight'] : $data[$dkeys]['weight'];
//                }else{
//                    $data[$dkeys]               =   $dv;
//                    $data[$dkeys]['metchNum']   =   1;
//                }
//            }
        }
        return $data;
    }
    
    public function ssort($arr,$w){
        uasort($arr,function($a,$b) use($w){
            //比对匹配次数
            if($a['metchNum'] > $b['metchNum']){
                $res    =   -1;
            }elseif($a['metchNum'] < $b['metchNum']){
                $res    =   1;
            }else{
                //比对位置
                if($a['weight'] > $b['weight']){
                    $res    =   1;
                }elseif($a['weight'] < $b['weight']){
                    $res    =   -1;
                }else{
                    //比对名字
                    if($a['name'] == $w){
                        $res    =   -1;
                    }elseif($b['name'] == $w){
                        $res    =   1;
                    }else{
                        //比对字数
                        if($a['wordCount'] > $b['wordCount']){
                            return  1;
                        }elseif($a['wordCount'] < $b['wordCount']){
                            return -1;
                        }else{
                            return 0;
                        }
                    }
                }
            }
            return $res;
        }); 
        return $arr;
    }
    
    

    
    
    
    
    
    
    public function setUpdate($sign,$oldsign,$limit){
        if($sign == $oldsign){
           echo '标记不能相同'; exit;
        }        
        if($sign   ==   -1 || $this->getServer('search.search_all')->where(array('status'=>$sign))->count() == 0){
            $this->getServer('search.search_all')->update(array('status'=>$sign),array('status'=>$oldsign),$limit);
        }
    }
    
    public function getDate($sign,$limit){
        $data   =   $this->getServer('search.search_all')->where(array('status'=>$sign))->limit($limit)->getAll()->toArray();
        return $data;
    }
    public function groupWords($words){
        $data   =   array();
        if(!empty($words)){
            $wordCount  =   count($words);
            $count      =   $wordCount < self::METCH_WORDS ? $wordCount : self::METCH_WORDS;
            for($i=0;$i<$wordCount-1;$i++){
                $data[$i]['zi'] =   strtolower($words[$i]['zi'].$words[$i+1]['zi']);
                $data[$i]['py'] =   strtolower($words[$i]['py'].$words[$i+1]['py']);
            }
            $data   =   array_slice(array_values(array_filter($data,function($v){
                return mb_strlen($v['zi'],'utf-8') > 1;
            })),0,$count);
            
        }
        return $data;
    }     */    
}