<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Script\Controller;
use Application\Base\Controller;
class CommentfilterController extends Controller
{
    
    
    public function indexAction() {
        set_time_limit(0);
        $review     =   $this->getServer('config')->filePath('Cache/review.txt');
        $pass       =   $this->getServer('config')->filePath('Cache/pass.txt');
        $reject     =   $this->getServer('config')->filePath('Cache/reject.txt');

        while($data       =   $this->getServer('Model\Shumei')->getComment()){
            foreach ($data as $v){
                $resJson    =   $this->getServer('Model\Shumei')->text($v['user_id'],$v['username'],$v['content']);
                if ($resJson["code"] == 1100) {
                    if ($resJson["riskLevel"] == "PASS") {
                        $filename   =   $pass;
                        // 放行
                    } else if ($resJson["riskLevel"] == "REVIEW") {
                        $filename   =   $review;
                            // 人工审核，如果没有审核，就放行
                    } else if ($resJson["riskLevel"] == "REJECT") {
                            // 拒绝
                        $filename   =   $reject;
                    } else {
                        // 异常
                        continue;
                    }
                    file_put_contents($filename, $v['content']."\n",FILE_APPEND);
                }
               
             } 
             echo date('Y-m-d H:i:s')."执行1000条\n";
        }
    }
}