<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Tool;

class Page{
    
    public $totalPage;  //总页数
    public $pagesInRange;           //显示的页码
    public $count;  //总条数
    public $countPerpage    =   10;//每页显示数
    public $current         =   0;    //当前页
    public $maxReval        =   10;   //最大显示页数    
    public $next            =   0;
    public $prev            =   0;
    public $first           =   0;
    public $end             =   0;
    public function __construct($count=0,$current=0,$countPerpage=10,$maxReval=10) {
        $this->setCount($count);
        $this->setCurrentPageNumber($current);
        $this->setCountPerpage($countPerpage);
        $this->setMaxReval($maxReval);
    }
    public function setCount($count){
        $this->count        =   $count;
        $this->totalPage    =   ceil($this->count/$this->countPerpage);  
        return $this;
    }
    public function offset(){
        if($this->current < 1){
            $this->current  =   1;
        }
        $num    =   $this->current > $this->totalPage && $this->totalPage > 0 ? $this->totalPage : $this->current;
        return $this->countPerpage*($num-1);
    }
    public function setCurrentPageNumber($num){
        $this->current  =   $num;
        $this->next     =   $num >= $this->totalPage ? 0 :$num+1;
        $this->prev     =   $num > 1 ? $num-1 : 0;   
        $this->first    =   $num > 1 ? 1 : 0;
        $this->end      =   $num < $this->totalPage ? $this->totalPage : 0;
        $left           =   $num -  (floor($this->maxReval/2)-1);
        $left           =   $left>0 ? $left : 1;
        $right          =   $left+($this->maxReval-1);
        if($right > $this->totalPage){
            $right  =   $this->totalPage;
            $left   =   $right-($this->maxReval-1) > 0 ? $right-($this->maxReval-1) : 1;
        }
        $this->pagesInRange =   array();
        for($i=$left;$i<=$right;$i++){
            $this->pagesInRange[] =   $i;
        }
        return $this;
    }
    public function setCountPerpage($num){
        $this->countPerpage =   $num;
        !empty($this->count)    &&  $this->totalPage = ceil($this->count/$this->countPerpage);
        !empty($this->current)  &&  $this->setCurrentPageNumber($this->current);
        return $this;
    }
    public function setMaxReval($num){
        $this->maxReval =   $num;
        !empty($this->current)  &&  $this->setCurrentPageNumber($this->current);
        return $this;
    }
}