<?php

//触发器管理

namespace Library\TriggerManager;
use SplObserver;
use SplObjectStorage;
class TriggerManager{
    private $observers;
    //添加触发器
    public function attach(SplObserver $observer) {
        if(!isset($this->observers)){
           $this->observers    =   new SplObjectStorage();
        }else if($this->observers->contains($observer)){
            return $this;
        }
        $this->observers->attach($observer);
        return $this;
    }
    //删除触发器
    public function detach(SplObserver $observer) {
        if(!empty($this->observers) && $this->observers->contains($observer)){
           $this->observers->detach($observer);
        }
        return $this;
    }
    //通知
    public function notify($serverName) {
        if(isset($this->observers)){
            foreach($this->observers as $observer){
                $observer->update($this);
            }
        }
    }

}
