<?php

namespace Application\Controller;
use Application\Base\Controller;
use Library\Application\Common;
use Library\Db\Adapter\Adapter;
class TempController extends Controller{
    public function bigtypetagAction(){
        $wktype     =   $this->getRequest()->getQuery('type');
        $subType    =   array_column($this->getServer('wukong214.v_type')->where(array('wktype'=>$wktype,'wksubtype!=""'))->getAll()->toArray(),'wksubtype');
        $bigtypes   =   array_column($this->getServer('wukong214.v_bigtype')->getAll()->toArray(),'code','name');
        
        $num            =   count($subType);
        $sort           =   range(0, $num);
        $rightRouter    =   array_fill(0, $num, 0);
        $rightRouter[0] =   18;
        $rightValue     =   array_fill(0, $num, '');
        $rightValue[0]  =   $wktype;
        $rightName      =   array_fill(0, $num, '');
        $rightName[0]   =   '筛选';
        $bottomValue    =   array_map(function($v) use($wktype){
            return $wktype.','.$v;
        },$subType);
        $columns        =   array('bigtype','videoType','rightRouter','rightValue',
            'rightName','bottomRouter','bottomValue','bottomName','subType','sort','wktype');
        $this->getServer('wukong214.v_bigtypetag')->batchInsert1($columns,$bigtypes[$wktype],'normal',$rightRouter,
                $rightValue,$rightName,20,$bottomValue,
                '查看更多',$subType,$sort,$wktype);
        return $this->responseSuccess();
    }
    public function indexAction() {
        
        $data   =   $this->getServer('wukong214.video_comment')->limit(50)->getAll()->toArray();
        $s1 = microtime(TRUE);
        $this->getServer('wukong214.video_comment')->getAdapter()->query('SET autocommit=0;',  Adapter::QUERY_MODE_EXECUTE);
         $this->getServer('wukong214.video_comment')->beginTransaction();
        foreach ($data as $v){
            $this->getServer('wukong214.video_comment')->update(array('content'=>'hehe'),array('id'=>$v['id']));
        }
        $this->getServer('wukong214.video_comment')->commit();
        $s2 = microtime(TRUE);
        
        echo ($s2-$s1)."<br>\n";
        $s1 = microtime(TRUE);
        foreach ($data as $v){
            $this->getServer('wukong214.video_comment')->update(array('content'=>'hehe'),array('id'=>$v['id']));
        }
        $s2 = microtime(TRUE);
        echo ($s2-$s1)."<br>\n";
exit;
    }
    public function importVideoAction() {
        //优酷
        $filePath       =   $this->config()->filePath('Cache/Tmp/1479455423964.json');
        $videoColumn    =   array(
                'cover','type','name','directors','actors','score','description','area',
                'tag','tnum','unum','showtime'
                );
        $playurlColumn  =   array('playurl');
        $data           =   $this->getJsonData($filePath);
        $video          =   Common::array_value($data, $videoColumn,'map');
      //  $playurl        =   Common::array_value($data, $playurlColumn,'map');
        $this->getServer('dianxin.video')->batchInsert($videoColumn,$video);
      //  $this->getServer('dianxin.video_youku')->batchInsert($playurlColumn,$playurl);
        exit;
        //优朋
        $filePath       = $this->config()->filePath('Cache/Tmp/dianbo.xls');
                $this->getServer('excel')->setOutputEncoding('utf-8');
        $this->getServer('excel')->read($filePath);
        $cells  =   $this->getServer('excel')->sheets[0]['cells'];
        print_r($cells);exit;
        echo $filePath;exit;
        /*
        
        
        $excelFile      =   $this->getRequest()->getFiles('excel');
        $this->getServer('excel')->setOutputEncoding('utf-8');
        $this->getServer('excel')->read($excelFile->tmp_name);
        $cells  =   $this->getServer('excel')->sheets[0]['cells'];
        $columnMap  =   array_column($this->tableConfig()->getTableConfig()->columnList,'name','comment');
        $columnKey  =   array_shift($cells);
        $columnName =   array_filter(array_map(function($v) use($columnMap){
            return isset($columnMap[$v]) ? $columnMap[$v] : '';
        },$columnKey));
        if(empty($cells)){
             return $this->responseError('数据不能为空');
        }
        if(count($columnName) != count($columnKey)){
            $diffColumn =   array_diff($columnKey, array_flip($columnName));
            return $this->responseError('文件数据错误,不存在列：'.  implode(',', $diffColumn));
        }        
        $this->selfTable()->batchInsert($columnName,$cells);*/
        return $this->responseSuccess();
    }
    public function getJsonData($file){
        return $this->getServer('file')->conn($file)->getByJson();
    }
    public function pushAction(){
        $uid    = $this->getRequest()->getQuery('uid');
        echo $this->getServer('Tool\Crypt')->encryptUserId($uid);exit;
    }
    public function unpushAction(){
        $uid    = $this->getRequest()->getQuery('uid');
        echo $this->getServer('Tool\Crypt')->decryptUserId($uid);exit;
    }
}