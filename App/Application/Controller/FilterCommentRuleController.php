<?php
/**
 * Created by PhpStorm.
 * User: mrgeneral mrgeneralgoo@gmail.com
 * Date: 16/3/3
 * Time: 上午10:58
 */

namespace Application\Controller;


use Application\Base\PublicController;
use Library\Application\Common;
use Library\Db\Sql\Predicate\Like;

class FilterCommentRuleController extends PublicController
{
    //添加操作
    public function doAddAction()
    {
        $words  =   $this->getRequest()->getPost('words');

        //上线后需要将此处修改为selfTable()
        $this->selfModel('Comment')->filter($words,function($v) use($words){
            return Common::regularFilter($v['content'], $words);
        });

        $this->curl('http://api.wukongtv.com/Comment/clearCache');
        return parent::doAddAction();
    }

    private function curl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}