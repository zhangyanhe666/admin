<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Controller;

class TagController extends BaseController{
    
    
    public function indexAction() {
        set_time_limit(0);
        $screen     =   $this->getService('Model\Video')->setDb('wukong')->getScreen();
        while ($data    =   $this->getService('Model\Video')->getVideo(10000)){
            $tagids     =   array();
            $wkids      =   array();
            foreach ($data as $v){
                $type       =   $v['wktype'];
                $tag        =   $v['tag'];
                $showtime   =   $v['showtime'];
                $area       =   $v['area'];
                if(!isset($screen[$type])){
                    continue;
                }
                if(isset($screen[$type]['tag']) && !empty($tag)){
                    $tags   =   explode(',', $tag);
                    $tagids    =   array_merge($tagids,array_values(array_intersect_key($screen[$type]['tag'], array_flip($tags))));
                }
                if(isset($screen[$type]['showtime']) && !empty($showtime) && isset($screen[$type]['showtime'][$showtime])){
                    $tagids[]  =   $screen[$type]['showtime'][$showtime];
                }
                if(isset($screen[$type]['area']) && !empty($area)){
                    $areas   =   explode(',', $area);
                    $tagids    =   array_merge($tagids,  array_values(array_intersect_key($screen[$type]['area'], array_flip($areas))));
                }
                $wkids  = array_pad($wkids, count($tagids), $v['wkid']);
            }
            if(!empty($tagids)){
                $this->getService('Model\Video')->addScreen($tagids,$wkids);
            }
        }
    }
}