<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Http;
use Library\Dom\Query;
class Curl{
    public $ch;
    public $result;
    public function __construct() {
        $this->ch   =   curl_init();
    }
    public function json($obj= false){
        $res    =   $obj == false ? false : array();
        if(!empty($this->result)){
            $res    =   json_decode($this->result,$obj);
        }
        return $res;
    }
    public function xml($encoding=null){
        return new Query($this->result,$encoding);
    }
    public function error(){
        return curl_error($this->ch);
    }
    public function errno(){
        return curl_errno($this->ch);
    }
    public function close(){
        curl_close($this->ch);
        return $this;
    }
    public function chInfo(){
        return curl_getinfo($this->ch);
    }
    public function exec($url){
        $this->setopt(CURLOPT_URL, $url)
             ->setopt(CURLOPT_RETURNTRANSFER, 1);
        $this->result   =   curl_exec($this->ch);
        return $this;
    }
    public function result(){
        return $this->result;
    }
    public function reset(){
        curl_reset($this->ch);
        return $this;
    }
    public function setopt($option,$value){
        curl_setopt($this->ch, $option, $value);
        return $this;
    }
}
