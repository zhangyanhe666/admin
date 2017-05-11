<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
use Application\Tool\Html;
use xmpush\IOSBuilder;
use xmpush\Builder;
use xmpush\ErrorCode;
class ImpushController extends PublicController{
    
    public function indexAction() {
        parent::indexAction();
        Html::delOption();
        Html::delTool();
    }
    public function doAddAction(){
        //请求参数接收
        $title          =   $this->getRequest()->getPost('title');
        $desc           =   $this->getRequest()->getPost('desc');
        $payload        =   $this->getRequest()->getPost('payload');
        $dev            =   $this->getRequest()->getPost('dev');
        $timeToSend     =   $this->getRequest()->getPost('timeToSend');
        $appver         =   $this->getRequest()->getPost('appver');
        $appnover       =   $this->getRequest()->getPost('appnover');
        $cover          =   $this->getRequest()->getPost('cover');
        //消息存储到消息中心
        $info['title']  =   $title;
        $info['cover']  =   $cover;
        $info['desc']   =   $desc;
        $info['action'] =   $payload;
        $info['dev']    =   $dev;
        $info['create_time']  =   !empty($timeToSend) ? $timeToSend : date('Y-m-d H:i:s');
        $this->selfModel('common_message')->add($info);
        $msgId          =   $this->selfModel('common_message')->getLastInsertValue();
        
        $payload        =   $payload."&msgid={$msgId}";
        $timeToSend     =   strtotime($timeToSend)*1000;
        //消息推送
        switch ($dev){
            case 'ios':                
                $builder = new IOSBuilder();
                $builder->title($title);  // 通知栏的title
                $builder->description($desc); // 通知栏的descption
                $builder->extra('payload', $payload); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
                $builder->badge('1');
                $builder->soundUrl('default');
                if(!empty($timeToSend)){
                    $builder->timeToSend($timeToSend);
                }
                $builder->build();
                $result =   $this->getServer('Tool\Impush')->iosSender()->broadcastAll($builder);
                break;
            case 'android':                
                $builder  =   new Builder();
                $builder->title($title);  // 通知栏的title
                $builder->description($desc); // 通知栏的descption
                $builder->passThrough(0);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
                $builder->payload($payload); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
                $builder->notifyId(0); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
                $builder->extra(Builder::notifyForeground, 1); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
                if(!empty($appver)){
                    $builder->extra('app_version', $appver); 
                }
                if(!empty($appnover)){
                   $builder->extra('app_version_not_in', $appnover);  
                }
                if(!empty($timeToSend)){
                    $builder->timeToSend($timeToSend);
                }
                $builder->build();
                $result =   $this->getServer('Tool\Impush')->androidSender()->broadcastAll($builder);
                break;
        }
        
        $this->getRequest()->getPost()->result  =   $result;
        if($result->getErrorCode() != ErrorCode::Success){
            //回滚消息
            $this->selfModel('common_message')->deleteById($msgId);
            return $this->responseError('推送失败');
        }

        parent::doAddAction();
    }
}