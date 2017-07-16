<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Tool\Tpl;

use Library\Application\Parameters;
use Application\Tool\User;
class TplTool extends \Application\Tool\Tool{
    public $item;
    public $judgeMap    =   array(0,1);
    private $hideColumns =   array();
    public function tableConfig(){
        return $this->getServer('Tool\TableConfig');
    }
    public function setItem($item){
        $this->item =   $item;
        return $this;
    }
    public function setHideColumn($columns){
        if(is_array($columns)){
            $this->hideColumns   =   \Library\Application\Common::merge($this->hideColumns, $columns);
        }  else {
            $this->hideColumns[] =   $columns;
        }
        return $this;
    }

    public function getItem($action,array $item,$callback=null){
        if(empty($item)){
            throw new \Exception(__CLASS__.'-'.$action.':item不能为空');
        }
        $this->setItem($item);
        foreach ($item as $column=>$value){
            $value      = is_callable($callback) ? $callback($value) : $value;
            $item[$column]  = $this->getColumn($action, $column, $value);
        }
        $item  =   array_filter((array)$item,function($v){
            return $v !== FALSE;
        });
        return $item;
    }
    
    /**
     * 处理器
     */
    
    //不使用类型
    public function Tunset(){
        return FALSE;
    }
    
    //原样显示类型
    public function original($value){
        return trim($value);
    }
    
    /***************
     * 列表页专用   *   
     ***************/
    //跳转编辑类型
    public function toedit($value,$column){
        $url        =   $this->_url($value, $column);
        $editUrl    =   !empty($url) ? $url : $this->getServer('router')->url(array('action'=>'edit'),array($column=>$value));
        $str        =   <<<TD
                <a   href="{$editUrl}">{$value}</a>
TD;
        return $str;
    }
    //列表页显示列表类型
    public function select($value,$column){
        $param      =   $this->tableConfig()->getColumnParam($column);
        $linkColumn =   $this->tableConfig()->getLinkTables()->$column;
        $changeValue      =   '';
        if(!empty($param->map) && isset($param->map[$value])){
            $changeValue  =   $param->map[$value];
        }elseif(!empty($linkColumn)){
            $changeValue  =   $this->item[$linkColumn->newColumn];
        }else{
            $changeValue  =   $value;
        }
        $value    =   $changeValue  ==   $value  ?  $value :   $changeValue."({$value})";
        return $param->linkType == 'outLink' ? $this->outLink($value,$column) : $this->interLink($value,$column);
    }
    //开关类型
    public function judge($value,$column){
        $param      =   $this->tableConfig()->getColumnParam($column);
        empty($param->val) && $param->val   =   $this->judgeMap;
        $value      =   strtoupper($param->keep) != 'N' ? $value : $param->val[1];
        return $value;
    }
    //图片显示类型
    public function img($value,$column){
        $url    =   $this->_url($value,$column);
        $str    =   <<<TD
        <a href="{$url}" target="_blank"><img src="{$url}" height="150px" /></a>
TD;
        return $str;
    }
        //图片显示类型
    public function addimg($value,$column){
        $comment    =   $this->tableConfig()->getColumn($column)->comment;
        $param      =   $this->tableConfig()->getColumnParam($column);
        $imgUrl     =   empty($value) ? '/assets/images/zhanwei.jpg' : $value;
        if(empty($param->w) || empty($param->h)){
            return $this->input($value, $column);
        }
        $str    =   <<<DIV
        <div class="form-group">
            <label for="name">{$comment}</label><br/>
            <img src="{$imgUrl}" style='margin-right: 10px;'/>
            <div style="border:1px #e5e5e5 solid;overflow:hidden; width:{$param->w}px; height:{$param->h}px;display:none;">
                <img src="/assets/images/zhanwei.jpg" style=""/>
            </div>
            <input type="text" name="{$column}" value="{$value}" placeholder=""/>
            <input type="button" class="btn btn-default"  onclick="admin.selectImg(this)" style="height:35px;weight:90px;" value='选图'/>
            <input type="button" class="btn btn-default"  onclick="admin.submitImge(this)" style="height:35px;weight:90px;"  value='选定'/>
        </div>
DIV;
        return $str;
    }
    //跳转到外部链接类型
    public function outLink($value,$column){
        $url    =   $this->_url($value,$column);
        $str    =   $value;
        if(!empty($url)){
        $str    =   <<<TD
        <a   href="{$url}" target="_blank">{$value}</a>
TD;
        }
        return $str;
    }
    //内部跳转链接
    public function interLink($value,$column){
        $url    =   $this->_url($value,$column);
        $str    =   $value;
        if(!empty($url)){
        $str    =   <<<TD
        <a   href="{$url}">{$value}</a>
TD;
        }
        return $str;
    }
    //列表编辑类型
    public function spanEdit($value,$column){
        $str    =   $value;
        if(isset($this->item['id'])){
        $str    =   <<<TD
                        <span onclick="admin.edit(this,{$this->item['id']})" style="height:50px;display:block;" field="{$column}" >{$value}</span>
TD;
        }
            return $str;
    }
    //列表缩减类型
    public function listquan($value){
        $content    =   str_replace(array("\r","\n","\r\n",),'',str_replace(array(' ','"'),array('&nbsp;','\\"'),$value));
        $str =   <<<TD
            <input type="button" value="全" onclick=admin.showText(this)>
            <div style="display:none;">{$content}</div>
TD;
        return $str;
    }
    
    //下拉列表类型
    public function selectList($value,$column){
        $comment    =   $this->tableConfig()->getColumn($column)->comment;
        $param      =   $this->tableConfig()->getColumnParam($column);
        $linkColumn =   $this->tableConfig()->getLinkTables()->{$column};
        $map        =   array();
        if($linkColumn){
            $map    =    array_column($this->getServer($linkColumn->linkDb.'.'.$linkColumn->linkTable)
                                    ->where($param->where)->getAll()->toArray(),$linkColumn->linkValue,$linkColumn->linkColumn);
        } 
        !empty($param->map) && $map    =   $param->map+$map;
        $option     =   \Library\Application\Common::option($map, $value);
        $multiple   =   $param->multiple ? 'multiple' : '';
        $str    =   <<<DIV
                <div class="form-group">
                    <label for="name">{$comment}</label>
                    <select class="form-control" {$multiple} name="{$column}">{$option}</select>
                </div>
DIV;
        return $str;
    }
    
    //列表页判断类型
    public function listJudge($value,$column){
        $param      =   $this->tableConfig()->getColumnParam($column);
        empty($param->val) && $param->val   =   $this->judgeMap;
        $checked    =   $param->val[0] == $value ? 'checked' : '';
        $str    =   $value;
        if(isset($this->item['id'])){
        $str    =   <<<TD
        <span onclick="admin.judge(this,{$this->item['id']})" field="{$column}" val="{$value}" param="{on:'{$param->val[0]}',off:'{$param->val[1]}'}" >
        <label>
            <input type="checkbox" {$checked} style="width:0px;" class="ace ace-switch ace-switch-6"> 
            <span class="lbl"></span> 
        </label>
        </span>            
TD;
        }
        return $str;
    }
    public function listweek($value,$column){
        $n  =   date('N');
        if(strpos($value,$n) !== FALSE){
            $value  =   "<i class=\"icon-heart\" style=\"color:red;\" ></i>{$value}";
        }
        return  $value;
    }
    public function week($value,$column){
        $comment    =   $this->tableConfig()->getColumn($column)->comment;
        $week       =   array('1','2','3','4','5','6','7');
        $weekList   =   implode('',array_map(function($v) use($value,$column){
            $checked=   strpos($value,$v) !== FALSE ? 'checked' : '';
            $check  =   <<<CHECK
            <div class="-week-div">  
            {$v}<input type="checkbox" name="{$column}[]" value="{$v}" {$checked}/>
            </div>
CHECK;
            return $check;
        },$week));
        $str    =   <<<DIV
                <style>
                    .-week-div{
                        width:60px;display:block;float:left;
                    }
                </style>
        <div class="form-group" style='height:60px;'>
            <label for="name">{$comment}</label><input type="checkbox" class="weekCheck{$column}"><br/>
            <input type="hidden"   name="{$column}" value="">
            {$weekList}
        </div>
        <script >
        $('.weekCheck{$column}').on('click',admin.weekCheck);
        </script>
DIV;
            //            <input type="text" class="form-control" id="{$column}" name="{$column}" value="{$value}" placeholder=""/>
        return $str;
    }
    public function doWeek($value){
        return !empty($value) ? implode(',', $value) : '';
    }
    private function param($column){
        return $this->tableConfig()->getColumnParam($column);
    }
    //获取url链接
    public function _url($value,$column){
        $param      =   $this->tableConfig()->getColumnParam($column);
        $url    =   '';
        if(strpos($value,'http') === 0){
            $url    =   $value;
        }elseif(!empty($param->url)){
            $url    =   $param->url;
            if(is_array($param->param)){
                $url   =   vsprintf($url, array_merge(array_flip($param->param),array_intersect_key($this->item,  array_flip($param->param))));
            }
        }elseif(!empty($param->menu)){
                $uri    =   $param->menu == 'index' ? array('action'=>  $this->getServer('router')->getAction()) : $param->menu;
                $from   =   !empty($param->from) ? $param->from : $column;
                $to     =   !empty($param->to) ? $param->to : $column;
                $uriParam   =   array('fieldName[]'=>$to,'fieldVal[]'=>$this->item[$from],'isLike'=>1);
                $url        =   $this->getServer('router')->url($uri,$uriParam);
        }
        return $url;
    }

    //输入框类型
    public function input($value,$column){
        $comment    =   $this->tableConfig()->getColumn($column)->comment;
        $str    =   <<<DIV
        <div class="form-group">
            <label for="name">{$comment}</label>
            <input type="text" class="form-control" id="{$column}" name="{$column}" value="{$value}" placeholder=""/>
        </div>
DIV;
        return $str;
    }
    //密码输入框类型(将js提出来)
    public function passwordInput($value,$column){
        $comment    =   $this->tableConfig()->getColumn($column)->comment;
        $str    =   <<<DIV
       <div class="form-group">
           <label for="name">{$comment}</label>
           <input type="password" class="form-control" id="{$column}" name="{$column}" value="" placeholder="">
       </div>         
       <div class="form-group"><label for="name">重复密码</label><input type="password" class="form-control" id="password1"  /></div>         
       <script type="text/javascript">
        function _check(){
            if($("#password").val().length<6){
                alert("密码不能小于6位");
                return false;
            }
            if($("#password").val()!=$("#password1").val()){
                alert("密码与重复密码不相同，请重新填写");
                return false;
            }
            return true;
        }
        </script>
DIV;
        return $str;
    }
    //隐藏域类型
    public function hidden($value,$column){
        $value  =   $this->tableConfig()->getColumnParam($column)->get('default',$value);        
        $str    =   <<<INPUT
                <input type="hidden"   name="{$column}" value="{$value}">
INPUT;
        return $str;
    }
    //大输入框类型
    public function text($value,$column){
        $comment    =   $this->tableConfig()->getColumn($column)->comment;
        $str    =   <<<DIV
         <div class="form-group">
             <label for="jianjie">{$comment}</label><br/>
             <textarea name="{$column}"  style="width:1000px;overflow: auto;min-height:150px;max-height: 250px;height: auto;box-sizing: content-box;">{$value}</textarea>
         </div>       
DIV;
        return $str;
    }
    //编辑器类型
    public function html($value,$column){
        $comment    =   $this->tableConfig()->getColumn($column)->comment;
        $value      =   htmlspecialchars_decode($value);
        $config     =   $this->tableConfig()->getColumnParam($column)->get('config','config.js');
        $str    =   <<<DIV
          <div class="form-group">
              <label for="jianjie">{$comment}</label><br/>
              <textarea class="ckeditor" cols="80" id="{$column}" name="{$column}" rows="10">{$value}</textarea>
          </div>
        <script>
        CKEDITOR.replace( '{$column}',{allowedContent: true,customConfig:"{$config}"});
        </script>  
DIV;
        return $str;
    }
    //输入时间类型
    public function dateFmt($value,$column){
            $comment    =   $this->tableConfig()->getColumn($column)->comment;
            $str    =   <<<DIV
            <div class="form-group">
                <label for="name">{$comment}</label>
                <input type="text" class="form-control" id="{$column}" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" name="{$column}" value="{$value}" placeholder=""/>
            </div>
DIV;
            return $str;
    }
    //搜索查询类型
    public function inputprompt($value,$column){
            $comment    =   $this->tableConfig()->getColumn($column)->comment;
            $str    =   <<<DIV
            <div class="form-group">
                <label for="name">{$comment}</label><br/>
                <input type="text" style="float:left;width:70%;margin-right:10px" class="form-control" id="{$column}" name="{$column}" value="{$value}" placeholder=""/>
                <input type="text" onKeyUp="admin.selectPrompt(this)" placeholder="查询" size=25 />
            </div>        
DIV;
            return $str;
    }
    //日期类型
    public function datetime(){
        return date('Y-m-d H:i:s');
    }
    //加密类型
    public function password($value){
        return  !empty($value) ? User::password($value) : false;
    }
    //收缩类型
    public function downquan($value){
        $content    =   str_replace(array("\r","\n","\r\n",),'',str_replace(array(' ','"'),array('&nbsp;','\\"'),$value));
        return $content;
    }
    //图片类型
    public function downimg($value,$column){
        return $this->_url($value,$column);
    }
    
    //添加操作排序
    public function doAddsort($value,$column){
        //$column
        $max    =   $this->getServer('Tool\DefaultTableServer')->db()->getColumn("max({$column})")+1;
        return $max;
    }
    
    public function getColumn($action,$column,$v){
        if($action  ==  'index' && in_array($column, $this->hideColumns)){
            return FALSE;
        }
        $procMap    =   array(
            'original'=>array(
                'index'=>'original',
             ),
            'defaultType'=>array(
                'add'=>'input',
                'edit'=>'input',
                'index'=>'spanEdit',
                'down'=>'original',
                'doAdd'=>'original',
                'doEdit'=>'original',
            ),
            'id'=>array(
                'add'=>'Tunset',
                'edit'=>'hidden',
                'index'=>'toedit',
             ),
            'sign'=>array(
                'add'=>'hidden',
                'edit'=>'hidden',
                'index'=>'Tunset',
             ),
            'password'=>array(
                'add'=>'passwordInput',
                'edit'=>'passwordInput',
                'index'=>'Tunset',
                'down'=>'Tunset',
                'doAdd'=>'password',
                'doEdit'=>'password',
             ),
            'shrinkage'=>array(
                'add'=>'text',
                'edit'=>'text',
                'index'=>'listquan',
                'down'=>'downquan',
             ),
            'createtime'=>array(
                'add'=>'hidden',
                'edit'=>'hidden',
                'doAdd'=>'datetime',
                'doEdit'=>'Tunset',
             ),
            'updatetime'=>array(
                'add'=>'Tunset',
                'edit'=>'hidden',
                'doAdd'=>'datetime',
                'doEdit'=>'datetime',
             ),
            'datetime'=>array(
                'add'=>'dateFmt',
                'edit'=>'dateFmt',
             ),
            'interLink'=>array(
                'index'=>'interLink',
             ),
            'outLink'=>array(
                'index'=>'outLink',
             ),
            'img'=>array(
                'index'=>'img',
                'add'=>'addimg',
                'edit'=>'addimg',
                'down'=>'downimg',
             ),
            'select'=>array(
                'add'=>'selectList',
                'edit'=>'selectList',
                'index'=>'select',
                'down'=>'select',
             ),
            'inputprompt'=>array(
                'add'=>'inputprompt',
                'edit'=>'inputprompt',
                'index'=>'select',
                'down'=>'select',
             ),
            'notUse'=>array(
                'add'=>'Tunset',
                'edit'=>'Tunset',
                'index'=>'Tunset',
                'down'=>'Tunset',
             ),
            'judge'=>array(
                'add'=>'Tunset',
                'edit'=>'Tunset',
                'index'=>'listJudge',
                'doAdd'=>'judge',
                'doEdit'=>'judge',
             ),
            'bootstrap'=>array(
                'add'=>'html',
                'edit'=>'html',
                'index'=>'Tunset',
                'down'=>'Tunset',
             ),
            NULL=>array(
                'add'=>'Tunset',
                'edit'=>'Tunset',
                'index'=>'Tunset',
                'down'=>'Tunset',
            ),
            'custom'=>array(
                'add'=>'Tunset',
                'edit'=>'Tunset',
                'index'=>'original',
            ),
            'sort'=>array(
                'add'=>'hidden',
                'edit'=>'hidden',
                'doAdd'=>'doAddsort',
                'doEdit'=>'Tunset',
                'index'=>'Tunset',
            ),
            'week'=>array(
                'add'=>'week',
                'edit'=>'week',
                'index'=>'listweek',
                'doAdd'=>'doWeek',
                'doEdit'=>'doWeek',
            ),
        );
        if(!isset($procMap['defaultType'][$action])){
            throw new \Exception('没有'.$action.'类型配置');
        }
        $viewType   =   $this->tableConfig()->getColumn($column)->viewType;
        if(!empty($this->tableConfig()->getColumn($column)->processor)){
            $processor  =   $this->tableConfig()->getColumn($column)->processor;
        }elseif(isset($procMap[$viewType][$action])){
            $processor  =   $procMap[$viewType][$action];
        }else{
            $processor  =   $procMap['defaultType'][$action];
        }
        return call_user_func(array($this,$processor),$v,$column);
    }
    public function typeRemark(){
        $typeRemark  =   array(
                        'original'=>array('val'=>'原样显示','placeholder'=>'无'),
                        'defaultType'=>array('val'=>'默认显示','placeholder'=>'无'),
                        'id'=>array('val'=>'id','placeholder'=>'无'),
                        'password'=>array('val'=>'密码','placeholder'=>'无'),
                        'shrinkage'=>array('val'=>'不完全显示','placeholder'=>'无'),
                        'createtime'=>array('val'=>'创建时间','placeholder'=>'无'),
                        'updatetime'=>array('val'=>'更新时间','placeholder'=>'无'),
                        'datetime'=>array('val'=>'时间类型','placeholder'=>'无'),            
                        'bootstrap'=>array('val'=>'html编辑类型','placeholder'=>'无'),
                        'custom'=>array('val'=>'外部字段','placeholder'=>'无'),
                        'notUse'=>array('val'=>'废弃','placeholder'=>'无'),
                        'sign'=>array('val'=>'标记类型','placeholder'=>'default=%s'),
                        'week'=>array('val'=>'周类型','placeholder'=>'无'),
                        'interLink'=>array('val'=>'内链接','placeholder'=>'url=链接 [param=n<参数] || menu=项目链接 [from=本表字段] [to=项目链接表字段与from有关联关系]'),
                        'outLink'=>array('val'=>'外链接','placeholder'=>'url=链接 [param=n<参数] || menu=项目链接 [from=本表字段] [to=项目链接表字段与from有关联关系]'),
                        'img'=>array('val'=>'图片','placeholder'=>'url=链接 [param=n<参数] || 无'),
                        'select'=>array('val'=>'列表','placeholder'=>'map=m<a:a,b:b [value=columnName [where=条件语句]] //需要外键'),
                        'inputprompt'=>array('val'=>'搜索','placeholder'=>'value=columnName [where=条件语句] //需要外键'),
                        'judge'=>array('val'=>'判断类型','placeholder'=>'无 || val=n<on,off'),
                        'sort'=>array('val'=>'排序类型','placeholder'=>'无')
                    );
        return $typeRemark;
    }
}