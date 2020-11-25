<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Controller;
use Application\Base\Controller;
use Library\Application\Common;
use xmpush\Builder;
use xmpush\IOSBuilder;
use xmpush\HttpBase;
use xmpush\Sender;
use xmpush\Constants;
use xmpush\Stats;
use xmpush\Tracer;
use xmpush\Feedback;
use xmpush\DevTools;
use xmpush\Subscription;
use xmpush\TargetedMessage;
class PushcommentController extends Controller{
    const SECRET            =   'hzsuirupnpd7Ve7p3ik9/g==';
    const APP_PACKAGENAME   =   'com.wukongtv.wkremote.client';
    const IOS_BUNDLE_ID     =   'com.wukongtv.remote';
    const IOS_SECRET        =   'I38c7ZNDEGHCU8gACNIytA==';
    public $message;
    public $sender;
    public $iosSender;
    public $targetMessage;
    public function androidSender(){
        if(!$this->sender){
            Constants::setPackage(self::APP_PACKAGENAME);
            Constants::setSecret(self::SECRET);
            $this->sender   =   new Sender();
        }
        return $this->sender;
    }
    public function iosSender(){        
        if(!$this->iosSender){
            Constants::setBundleId(self::IOS_BUNDLE_ID);
            Constants::setSecret(self::IOS_SECRET);
            Constants::useSandbox();
            $this->iosSender   =   new Sender();
        }
        return $this->iosSender;
    }
    public function scriptStart(){
        $date   =   date('Y-m-d H:i:s');
        echo "脚本{$this->router()->getAction()}开始执行:{$date}\n";
        return microtime(TRUE);
    }
    public function scriptEnd($startTime){
        $endTime  =   microtime(TRUE);
        $allTime  =   $endTime-$startTime;
        echo "脚本{$this->router()->getAction()}执行结束\n执行总时长：{$allTime}s\n";
        exit;
    }
    public function onDispatch() {
        set_time_limit(0);
        $time   =   $this->scriptStart();
        parent::onDispatch();
        $this->scriptEnd($time);
    }
    public function pushscriptAction(){
        $targetMessageList  =   array();
        $commentList        =   $this->getService('Model\Comment')->commentList();
        if(!empty($commentList)){
            foreach ($commentList as $v){
                if($v['dev'] == 'ios'){
                    $targetMessageList =   $this->iospush($payload);
                    print_r($this->iosSender()->sendToAliases($targetMessageList,array($alias))->getRaw());
                }else{                    
                    $targetMessageList =   $this->push($v['alias'], $v['payload']);
                    print_r($this->androidSender()->multiSend(array($targetMessageList),TargetedMessage::TARGET_TYPE_USER_ACCOUNT)->getRaw());
                }           
   
            }            
        }

    }
    public function push($alias,$payload){
        if(empty($payload['content'])){
            $title  =   $payload['username'].'赞了你的评论';
        }else{
            $title  =    $payload['username'].'回复了你';
        }
        $message  =   new Builder();
        $targetMessage = new TargetedMessage();
        $message->title($title);  // 通知栏的title
        $message->description($payload['content'].' '); // 通知栏的descption
        $message->passThrough(0);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
        $message->payload(json_encode($payload)); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
        $message->extra(Builder::notifyForeground, 1); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
        $message->notifyId(0); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
        $message->build();
        $targetMessage->setTarget($alias, TargetedMessage::TARGET_TYPE_USER_ACCOUNT); // 设置发送目标。可通过regID,alias和topic三种方式发送
        $targetMessage->setMessage($message);
        return $targetMessage;
    }
    public function iospush($payload){
        if(empty($payload['content'])){
            $title  =   $payload['username'].'赞了你的评论';
        }else{
            $title  =    $payload['username'].'回复了你';
        }
        $message = new IOSBuilder();
        
        $message->title($title);  // 通知栏的title
        $message->description($payload['content']); // 通知栏的descption
        $message->extra('payload', json_encode($payload)); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
        $message->badge('1');
        $message->soundUrl('default');
        $message->build();
        return $message;
    }
    public function testpushAction(){
        
        $payload["username"]    =   "一叶知秋";
        $payload["headimgurl"]    =   "http: //wx.qlogo.cn/mmopen/PiajxSqBRaEKtGsZJdAlOtfJG1vDU16v05rZWHzHy1colS3Pfy90sYickTVPRY8Qic320Y2Lb6Sgq86gmYy5KJSRQ/0";
        $payload["content"]    =   "一叶知秋";
        $payload["create_time"]    =   "刚刚";
        $payload["msgtype"]    =   "comment";
        $payload["cid"]    =   "8314";
        $payload["wkid"]    =   "551493";
        $payload["id"]    =   "1";

        $targetMessage  =   $this->iospush($payload);
        print_r($this->iosSender()->sendToAliases($targetMessage,array('281ffb56368c1f68ee2a7cdf3a08bb9bbca0c8e4'))->getRaw());
    }
    public function subscribeAction(){
        $time   =   date('Y-m-d H:i:s');
        $starttime  =   date('H:i',time() + 600);
        $n      =   date('N');
        $nowtime   =   time();
        $items  =   $this->getService('wukong.tonight')->where(array('starttime'=>$starttime,"'{$time}' > online and '{$time}' < offline",'switch'=>1,"find_in_set({$n},week)"))->getAll()->toArray();
        if(!empty($items)){
            $showids    =   array_column($items,'id');
            $subscribe  =   $this->getService('wukong.subscribe')->where(array('show_id'=>$showids))->getAll()->toArray();
            if(!empty($subscribe)){
                $data   =   Common::arrayResetKey($items, 'id');
                foreach ($subscribe as $v){
                    $this->getService('wukong.push_comment')->insert(array('touser_id'=>$v['user_id'],
                        'content'=>  json_encode($data[$v['show_id']])
                        ,'create_time'=>date('Y-m-d H:i:s')));
                }
            }
        }
    }
    
    public function pushsubscribeAction(){
        $commentList        =   $this->getService('Model\Comment')->subcribe();
        if(!empty($commentList)){
            foreach ($commentList as $v){
                $vcontent    =   json_decode($v['content'],TRUE);
                $title      =   '预约通知';
                $content    =   "您预约\"{$vcontent['showname']}\"将在10分钟后开始";
                $image      =   !empty($vcontent['subscribe_cover']) ? $vcontent['subscribe_cover'] : $vcontent['cover'];
                $payload    =   "wukongtv://main?showid={$vcontent['id']}&messagePic=".urlencode($image);
                $alias      =   $v['realId'];
                if($v['dev'] == 'ios'){
                    $message    =   $this->iosMessage($title, $content, $payload,$v['id']);
                    print_r($this->iosSender()->sendToAliases($message,array($alias))->getRaw());
                }else{
                    $message    =   $this->message($alias,$title, $content, $payload,$v['id']);
                    print_r($this->androidSender()->multiSend(array($message),TargetedMessage::TARGET_TYPE_USER_ACCOUNT)->getRaw());
                }
            }
        }
    }
    
    public function testpushsubscribeAction(){
        $realId    = $this->getRequest()->getQuery('realId');
        $showId    = $this->getRequest()->getQuery('showId');
        $dev        =   $this->getRequest()->getQuery('dev');
        $showName   =   $this->getService('wukong214.tonight')->where(array('id'=>$showId))->getColumn('showname');
        $title      =   '预约通知';
        $content    =   "您预约\"{$showName}\"将在10分钟后开始";
        $payload    =   "wukongtv://main?showid=".$showId;
        $alias      =   $realId;
        if($dev == 'ios'){
            $message    =   $this->iosMessage($title, $content, $payload,'1000');
            print_r($this->iosSender()->sendToAliases($message,array($alias))->getRaw());
        }else{
            $message    =   $this->message($alias,$title, $content, $payload,'1000');
            print_r($this->androidSender()->multiSend(array($message),TargetedMessage::TARGET_TYPE_USER_ACCOUNT)->getRaw());
        }
        
       
    }
    
    public function message($alias,$title,$content,$payload,$id){
        $message  =   new Builder();
        $targetMessage = new TargetedMessage();
        $message->title($title);  // 通知栏的title
        $message->description($content); // 通知栏的descption
        $message->passThrough(0);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
        $message->payload($payload); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
        $message->extra(Builder::notifyForeground, 1); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
        $message->extra('id', $id); // id
        $message->notifyId(0); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
        $message->build();
        $targetMessage->setTarget($alias, TargetedMessage::TARGET_TYPE_USER_ACCOUNT); // 设置发送目标。可通过regID,alias和topic三种方式发送
        $targetMessage->setMessage($message);
        return $targetMessage;
    }
    public function iosMessage($title,$content,$payload,$id){
        $message = new IOSBuilder();
        $message->title($title);  // 通知栏的title
        $message->description($content); // 通知栏的descption
        $message->extra('payload', $payload); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
      //  $message->badge('1');
        $message->extra('id', $id); // id
        $message->soundUrl('default');
        $message->build();
        return $message;
    }
    //304预约入消息中心和推送中心
    public function subscribeToPush(){
        
        echo "预约推送开始:".date('Y-m-d H:i:s')."\n";
        $count      =   0;
        $subscribe  =   $this->getService('Model\Push')->getSubscribe();
        if(!empty($subscribe)){
            //消息中心入库
            //消息存储到消息中心
            $title      =   '预约通知';
            foreach ($subscribe as $v){
                $info['title']  =   $title;
                $info['user_id']=   $v['user_id'];
                $info['cover']  =   !empty($v['subscribe_cover']) ? $v['subscribe_cover'] : $v['cover'];
                $info['desc']   =   "您预约\"{$v['showname']}\"将在10分钟后开始";
                $info['action'] =   "wukongtv://main?showid={$v['id']}";

                $this->selfModel('login_message')->add($info);
                $msgId          =   $this->selfModel('login_message')->getLastInsertValue();
                $payload[]      =   "wukongtv://main?showid={$v['id']}&msgId={$msgId}";
                $desc[]         =   $info['desc'];
                $dev[]          =   $v['dev'];
                $realId[]       =   $v['realId'];
            }

            $count  =   count($subscribe);
            //推送消息入库
            $this->getService('Model\Push')->toPush($title,$realId,$desc,$payload,$dev);
        }
        echo "预约推送结束：".date('Y-m-d H:i:s').";总推送条数：($count)\n";
    }
    //单条数据推送
    public function pushToIm(){
        echo "单条推送开始:".date('Y-m-d H:i:s')."\n";
        $count      =   0;
        $pushList   =   $this->getService('Model\Push')->getPush();
        if(!empty($pushList)){
            foreach ($pushList as $im){
                //消息推送
                switch ($im['dev']){
                    case 'ios':                
                        $builder = new IOSBuilder();
                        $builder->title($im['title']);  // 通知栏的title
                        $builder->description($im['desc']); // 通知栏的descption
                        $builder->extra('payload', $im['payload']); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
                        $builder->badge('1');
                        $builder->soundUrl('default');
                        $builder->build();
                        $result =   $this->iosSender()->send($builder,$im['realId']);
                        break;
                    case 'android':                
                        $builder  =   new Builder();
                        $builder->title($im['title']);  // 通知栏的title
                        $builder->description($im['desc']); // 通知栏的descption
                        $builder->passThrough(0);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
                        $builder->payload($im['payload']); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
                        $builder->notifyId(0); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
                        $builder->extra(Builder::notifyForeground, 1); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
                        $builder->build();
                        $result =   $this->androidSender()->send($builder,$im['realId']);
                        break;
                }
                $this->getService('Model\Push')->updatePush($im['id'],$result);
            }
            $count  =   count($pushList);
        }
        echo "单条推送结束：".date('Y-m-d H:i:s').";总推送条数：($count)\n";
    }
}