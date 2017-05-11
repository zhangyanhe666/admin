<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Library\Application\Common;
class DanmuController extends \Application\Base\PublicController{
    
    public function doAddAction() {
        parent::doAddAction();
        $wkid       =   $this->selfTable()->getLastInsertValue();
        $videoUrl   =   $this->getRequest()->getPost('videoUrl');
        if(Common::isValidUrl($videoUrl)){
            //$dom    =   new \DOMDocument();
            //$dom->loadHTML(htmlentities($html));
            if(strpos($videoUrl,'bilibili')){
                preg_match('/av(.*).html/',$videoUrl,$metch);
                if(!empty($metch[1])){
                    $commentUrl =   "http://api.bilibili.com/playurl?aid={$metch[1]}&page=1";
                    if(Common::isValidUrl($commentUrl)){
                        $comdata    =   json_decode($this->curl($commentUrl),TRUE);
                        if(isset($comdata['cid'])){
                            $danmu_xml  =   $comdata['cid'];
                            $xml    =   $this->curl($danmu_xml);
                            $p      =    xml_parser_create();
                            xml_parse_into_struct($p, $xml, $items,$index);
                            xml_parser_free($p);
                            $info   =   array();
                            $infos  =   array();
                            foreach ($index['D'] as $key){
                                if(!isset($items[$key]['value'])){
                                    continue;
                                }
                                $attr_p             =   explode(',', $items[$key]['attributes']['P']);
                                $info['wkid']       =   $wkid;
                                $info['uid']        =   0;
                                $info['content']    =   $items[$key]['value'];
                                $info['color']      =   16777215;
                                $info['timeline']   =   $attr_p[0];
                                $info['deviceid']   =   -1;
                                $infos[]            =   $info;
                            }
                            $this->selfModel('danmu_comment')->batchInsert(array_keys($infos[0]),$infos);
                        }
                    }
                }
            }
        }
    }
    public function curl($url){
        $xml    =   $this->getServer('curl')
                                    ->setopt(CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)')
                                    ->setopt(CURLOPT_PROXYTYPE, CURLPROXY_HTTP)
                                    ->setopt(CURLOPT_PROXYAUTH, CURLAUTH_BASIC)
                                    ->setopt(CURLOPT_PROXY,'124.88.67.63')
                                    ->setopt(CURLOPT_PROXYPORT,80)
                                    ->setopt(CURLOPT_AUTOREFERER,1)
                                    ->setopt( CURLOPT_ENCODING,'gzip')
                                    ->exec($url)->result();
        return $xml;
    }
}
