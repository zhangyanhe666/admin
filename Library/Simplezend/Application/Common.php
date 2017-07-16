<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Library\Application;

class Common{

    public static $showError    =   false;
    public static $timeAnchor   =   array();
    public static function arrayResetKey($arr,$key){
        if(!empty($arr)){
            if($arr instanceof \Library\Application\Parameters){
                $arr    =   $arr->toArray();
            }
            $keys           =   array_column($arr,$key);
            if(empty($keys)){
                throw new \Exception('arrayResetKey arr数组中没有key：'.$key);
            }
            $arr            =   array_combine($keys,$arr );
        }
        return $arr;
    }
    public static function arrayCateKey($arr,$key){
        if(!empty($arr)){
            if($arr instanceof \Library\Application\Parameters){
                $arr    =   $arr->toArray();
            }
            $keys           =   array_unique(array_column($arr,$key,$key));
            if(empty($keys)){
                throw new \Exception('arrayResetKey arr数组中没有key：'.$key);
            }
            $arr    =   array_map(function($v) use($arr,$key){
                return array_filter($arr,function($vv) use($v,$key){
                    return $vv[$key]    ==  $v;
                });
            }, $keys);
        }
        return $arr;
    }
    public static function arrayResetObj($arr,$key){
        $arr    = self::arrayResetKey($arr, $key);
        return new \Library\Application\Parameters($arr);
    }
    public static function strToArr($str,$split,$keySplit){
        $exArr  =   explode($split,trim($str,$split));
        $fiArr  =   array_filter($exArr,function($v) use($keySplit){
                        return strpos(trim($v,$keySplit),$keySplit);
                    });
        $maArr  =   array_map(function($v) use($keySplit){
                        $key         =   strstr($v, $keySplit,true);
                        $val         =   trim(strstr($v,$keySplit),$keySplit);
                        return array('key'=>$key,'val'=>$val);
                    },$fiArr);
        $arr   =    array_column($maArr,'val','key');
        return $arr;
    }

    public static function strToMap($str){
        $res    =   array();
        if(!empty($str)){
            $map    =   self::strToArr($str, ' ', '=');
            if(!empty($map)){
                foreach ($map as $mk=>$mv){
                    $tag   =   substr($mv,0,2);
                    $tval  =   substr($mv,2);
                    switch ($tag){
                        case 'a<':
                            $tval   =   explode(',',trim($tval,','));
                            $mval   =   array_combine($tval, $tval);
                            break;
                        case 'n<':
                            $mval   =   explode(',',trim($tval,','));
                            break;  
                        case 'm<':
                            $mval   =   self::strToArr($tval, ',', ':');
                            break;   
                        default :
                            $mval   =   $mv;
                    }
                    $res[$mk]     =   $mval;
                }
            }
        }
        return new \Library\Application\Parameters($res); 
    }
    //判断数组是否是索引数组
    public static function is_assoc($arr){
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    
    public static function subTitle($action){ 
        $title  =   array(
                        'index'=>'列表',
                        'add'=>'添加',
                        'edit'=>'编辑',
                        'usercenter'=>'个人信息',
                    );
        return isset($title[$action]) ? $title[$action] : false;
    }
    public static function mb_sub($str,$s,$l){
        $res    =   mb_substr($str,$s,$l,'utf-8');
        return $res == $str     ? $str : $res.'...';
    }
    public static function merge($arr,$arr1){
        if(empty($arr)){
            return $arr1;
        }
        if(empty($arr1)){
            return $arr;
        }
        return array_merge($arr,$arr1);
    }

    public static function getSqlError($exc){
        if( $exc instanceof \Library\Db\Adapter\Exception\InvalidQueryException){
            $errorInfo  =   $exc->getPrevious()->errorInfo;
            $error  =   array(
                    '1005'=>'创建表失败',
                    '1006'=>'创建数据库失败',
                    '1007'=>'数据库已存在，创建数据库失败',
                    '1008'=>'数据库不存在，删除数据库失败',
                    '1009'=>'不能删除数据库文件导致删除数据库失败',
                    '1010'=>'不能删除数据目录导致删除数据库失败',
                    '1011'=>'删除数据库文件失败',
                    '1012'=>'不能读取系统表中的记录',
                    '1020'=>'记录已被其他用户修改',
                    '1021'=>'硬盘剩余空间不足，请加大硬盘可用空间',
                    '1022'=>'关键字重复，更改记录失败',
                    '1023'=>'关闭时发生错误',
                    '1024'=>'读文件错误',
                    '1025'=>'更改名字时发生错误',
                    '1026'=>'写文件错误',
                    '1032'=>'记录不存在',
                    '1036'=>'数据表是只读的，不能对它进行修改',
                    '1037'=>'系统内存不足，请重启数据库或重启服务器',
                    '1038'=>'用于排序的内存不足，请增大排序缓冲区',
                    '1040'=>'已到达数据库的最大连接数，请加大数据库可用连接数',
                    '1041'=>'系统内存不足',
                    '1042'=>'无效的主机名',
                    '1043'=>'无效连接',
                    '1044'=>'当前用户没有访问数据库的权限',
                    '1045'=>'不能连接数据库，用户名或密码错误',
                    '1048'=>'字段不能为空',
                    '1049'=>'数据库不存在',
                    '1050'=>'数据表已存在',
                    '1051'=>'数据表不存在',
                    '1054'=>'字段不存在',
                    '1065'=>'无效的SQL语句，SQL语句为空',
                    '1081'=>'不能建立Socket连接',
                    '1114'=>'数据表已满，不能容纳任何记录',
                    '1116'=>'打开的数据表太多',
                    '1129'=>'数据库出现异常，请重启数据库',
                    '1130'=>'连接数据库失败，没有连接数据库的权限',
                    '1133'=>'数据库用户不存在',
                    '1141'=>'当前用户无权访问数据库',
                    '1142'=>'当前用户无权访问数据表',
                    '1143'=>'当前用户无权访问数据表中的字段',
                    '1146'=>'数据表不存在',
                    '1147'=>'未定义用户对数据表的访问权限',
                    '1149'=>'SQL语句语法错误',
                    '1158'=>'网络错误，出现读错误，请检查网络连接状况',
                    '1159'=>'网络错误，读超时，请检查网络连接状况',
                    '1160'=>'网络错误，出现写错误，请检查网络连接状况',
                    '1161'=>'网络错误，写超时，请检查网络连接状况',
                    '1062'=>'字段值重复，入库失败',
                    '1169'=>'字段值重复，更新记录失败',
                    '1177'=>'打开数据表失败',
                    '1180'=>'提交事务失败',
                    '1181'=>'回滚事务失败',
                    '1203'=>'当前用户和数据库建立的连接已到达数据库的最大连接数，请增大可用的数据库连接数或重启数据库',
                    '1205'=>'加锁超时',
                    '1211'=>'当前用户没有创建用户的权限',
                    '1216'=>'外键约束检查失败，更新子表记录失败',
                    '1217'=>'外键约束检查失败，删除或修改主表记录失败',
                    '1226'=>'当前用户使用的资源已超过所允许的资源，请重启数据库或重启服务器',
                    '1227'=>'权限不足，您无权进行此操作',
                    '1235'=>'MySQL版本过低，不具有本功能'
            );
            return isset($error[$errorInfo['1']]) ? $error[$errorInfo['1']].";\n mysql:".$errorInfo[2] : implode(',', $errorInfo);

        }else{
            return $exc->getMessage();
        }
    }
    //获取select的option
    public static function option($list,$defaultVal='',$map=array('key'=>'','val'=>'')){
        $option     =   '';
        foreach($list as $k=>$v){
            $key        =   !empty($map['key']) ? $v[$map['key']] : $k;
            $val        =   !empty($map['val']) ? $v[$map['val']] : $v;
            $selected   =   $key == $defaultVal  ? 'selected' : "";
            $option     .=  "<option value=\"{$key}\" {$selected}>{$val}</option>";
        }
        return $option;
    }
    public static function downCsv($fileName,$data,$downcode){
        setlocale(LC_ALL, 'en_US.UTF-8');
        Header("Content-type: text/csv");
        Header("Accept-Ranges: bytes");
        Header("Content-Disposition: attachment; filename={$fileName}.csv");
        $content    =   '';
        if(!empty($data)){
            foreach ($data as $dv){
                $content    .=   implode(',', $dv)."\n";
            }
        }
        if($downcode != 'utf-8'){
            $content    =   mb_convert_encoding($content,"gb2312","utf-8");
        }        
        echo $content;exit;
    }
    public static function arrUasort($arr,$key,$order='asc'){
        $order  =   $order == 'asc' ? '1' : '-1';
        uasort($arr,function($a,$b) use($key,$order){
            if(isset($a[$key]) && isset($b[$key])){
                $a[$key]>$b[$key] && $res    =    $order;
                $a[$key]==$b[$key] && $res   =    0;
                $a[$key]<$b[$key] && $res    =    -$order;       
            }else{
                $res    =   0;
            }
            return $res;
        }); 
        return $arr;
    }
   /** 
    * 10进制转为62进制 
    *  
    * @param integer $n 10进制数值 
    * @return string 62进制 
    */ 
    public static function dec62($n) {  
        $base = 62;  
        $index = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';  
        $ret = '';  
        for($t = floor(log10($n) / log10($base)); $t >= 0; $t --) {  
            $a = floor($n / pow($base, $t));  
            $ret .= substr($index, $a, 1);  
            $n -= $a * pow($base, $t);  
        }  
        return $ret;  
    }
    /** 
     * 62进制转为10进制 
     * 
     * @param integer $n 62进制 
     * @return string 10进制 
     */ 
    public static function dec10($s) {  
        $base = 62;  
        $index = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';  
        $ret = 0;  
        $len = strlen($s) - 1;  
        for($t = 0; $t <= $len; $t ++) {  
            $ret += strpos($index, substr($s, $t, 1)) * pow($base, $len - $t);  
        }  
        return $ret;
    }

    /**
     * @param $text
     * @param $modelText
     * @return int
     * 文本相似度检测
     */
    public static function textModelFilter($text,$modelText){
        $preLength = 15;
        $prePercent = 80;
        if(strlen($text) <= $preLength)
            return 0;
        similar_text($text,$modelText,$percent);
        if($percent >= $prePercent)
            return 1;
        return 0;
    }

    /**
     * @param $text
     * @param $rule
     * @return int
     * 文本规则检测
     */
    public static function regularFilter($text,$rule){
        return preg_match("/".preg_quote($rule)."/",$text);
    }
    public static function checkStrCode($str){
        $encoding   =   '';
        $strlen =   strlen($str);
        for($i=0;$i<$strlen;$i++){
            if (ord($str{$i}) < 128){  
                continue;
            }else{
                $encoding = "unknown";
                if ((ord($str{$i}) & 224) == 224) {  
                    //第一个字节判断通过   
                    $char = $str{++$i};  
                    if ((ord($char) & 128) == 128) {  
                        //第二个字节判断通过   
                        $char = $str{++$i};  
                        if ((ord($char) & 128) == 128) {  
                            $encoding = "UTF-8";
                            break;
                        }  
                    }  
                }
                if ((ord($str{$i}) & 192) == 192) {  
                    //第一个字节判断通过   
                    $char = $str{++$i};  
                    if ((ord($char) & 128) == 128) {  
                        //第二个字节判断通过   
                        $encoding = "GB2312";  
                        break;  
                    }  
                }
            }
        }
        return $encoding;
    }
    
    public static function charType($ord,$ord2=''){
        //gbk汉字
        $code   =   0;
        if((($ord & 192) == 192 || ($ord & 128) == 128) && ($ord2 & 128) == 128){
            $code   =   self::unicode_encode(chr($ord).chr($ord2));
        }
        if ( $code > 19967 && $code < 40870) {
            return 1;            
        }
        //字母97-122，65-90
        elseif(($ord>96 && $ord<123) || ($ord>64 && $ord<91)){
            return 2;
        }
        //数字48-57
        elseif(($ord>47 && $ord<58)){
            return 3;
        }
        //空格
        elseif($ord == 32){
            return 4;
        }
        return 0;
    }
    
    /**
    * $str 原始中文字符串
    * $encoding 原始字符串的编码，默认GBK
    * $prefix 编码后的前缀，默认"&#"
    * $postfix 编码后的后缀，默认";"
    */
   public static function unicode_encode($str, $encoding = 'GBK') {
       $str = mb_convert_encoding($str, 'UCS-2', $encoding);
       $dec = hexdec(bin2hex($str));
       return $dec;
   } 
   public static function isValidUrl($url){
       if(!empty($url)){
           $head    =   get_headers($url);
           if(strpos($head[0],'200') != false){
               return true;
           }
       }
       return false;
   }

    //获取指定键的
    public static function array_value(array $arr,array $keys,$type=''){
        if(empty($keys)){
            return array();
        }
        $keys   =   array_flip($keys);
        if($type == 'map'){
            $data   =   array_map(function($v) use($keys){
                return array_intersect_key($v, $keys);
            },$arr);
        }else{
            $data                   =   array_intersect_key($arr, $keys);
        }
        return $data;
    }
    public static function mval($data,$key,$sym='min'){
        $m            =    array_reduce($data,function($a,$b) use($key,$sym){
            if(empty($a)){
                return $b[$key];
            }
            if($sym == 'min'){
                return $b[$key] < $a ? $b[$key] : $a;
            }else{
                return $b[$key] > $a ? $b[$key] : $a;
            }
        });
        return $m;
    }
    //跨域
    public static function cors(){
        header("Access-Control-Allow-Origin:*");
    }
    //获取分类及子项
    public static function array_cate($cate,$type,$sub){
        $data   =   array();
        foreach ($cate as $k=>$v){
            $data[$v[$type]][]     =   $v[$sub];
        }
        return $data;
    }
    public static function library($path){
        include realpath('./Library/'.trim($path));
    }
    //计时器
    public static function setTimeAnchor($k){
        self::$timeAnchor[$k]   = microtime(TRUE);
    }
    public static function getTimeAnchor(){
        $timeAnchor =   array();
        foreach (self::$timeAnchor as $k=>$v){
            $arr[]  =   $k;
            foreach (self::$timeAnchor as $kk=>$vv){
                !in_array($kk,$arr) && $timeAnchor[$k][$kk] =   round($vv-$v,3);
            }
        }
        return $timeAnchor;
    }
    public static function replace_tag($uri,$param,$tag){
        if(!empty($param)){
            $search         =   array_map(function($v) use($tag){
                return $tag.$v;
            },array_keys($param));
            $replace        =   array_values($param);
            $uri            =   str_replace($search,$replace,$uri);
        }
        return $uri;
    }
}
