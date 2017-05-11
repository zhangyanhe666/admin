<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library\Application;
use Library\Application\Parameters;
class Request{
    protected $envParams;
    protected $queryParams;
    protected $postParams;
    protected $cookieParams;
    protected $serverParams;
    protected $fileParams;
    protected $content;
    protected $opt;
    public $host;
    protected $uri;
    public $queryString;
    public $local;
    public $requestMethod;
    protected $isScript =   false;
    public function __construct()
    {
        $this->setEnv(new Parameters($_ENV));
        $this->setArgv();
        if ($_GET) {
            $this->setQuery(new Parameters($_GET));
        }
        if ($_POST) {
            $this->setPost(new Parameters($_POST));
        }
        if ($_COOKIE) {
            $this->setCookies(new Parameters($_COOKIE));
        }
        if ($_FILES) {
            $files = $this->mapPhpFiles();
            $this->setFiles(new Parameters($files));
        }
        $this->setServer(new Parameters($_SERVER));
    }
    public function getContent(){
        if(empty($this->content)){
            $requestBody    = file_get_contents('php://input');
            if(strlen($requestBody)>0){
                $this->content  =   $requestBody;
            }
        }
        return $this->content;
    }
    public function setEnv($env){
        $this->envParams  =   $env;
        return $this;
    }
    public function setQuery($query){
        $this->queryParams  =   $query;
        return $this;
    }
    public function setPost($post){
        $this->postParams  =   $post;
        return $this;
    }
    public function setFiles($files){
        $this->fileParams   =   $files;
        return $this;
    }
    public function setCookies($cookie){
        $this->cookieParams  =   $cookie;
        return $this;
    }
    public function setArgv(){
        $queryStr =   '';
        if(($u  =   getopt('u:')) != false){
                $uu =   $u['u'];
                if (($pos   =   strpos($uu, '?')) !== false) {
                    $queryStr    =   substr($uu,$pos+1);
                }
        }
        $get    =   \Library\Application\Common::strToArr($queryStr, ':', '=');
        $this->setQuery(new Parameters($get));
    }
    public function setServer($server){
        $this->serverParams =   $server;
        $this->host         =   $server->get('REQUEST_SCHEME','http').'://'.$server->get('HTTP_HOST');
        $this->local        =   $server->get('SERVER_NAME');
        $this->queryString  =   $server->get('REDIRECT_QUERY_STRING','');
        $this->requestMethod=   strtolower($server->get('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest' ? 'ajax' : strtolower($server->get('HTTP_X_REQUESTED_WITH'));        
        return $this;
    }
    public function script(){
        return $this->isScript;
    }
    public function getUri(){
        if(empty($this->uri)){
            //使用脚本路由
            if(($u  =   getopt('u:')) != false){
                $uri    =   $u['u'];
                $this->isScript =   TRUE;
            }else{
                $uri    =   $this->getServer('REQUEST_URI');
            }
            if (($pos   =   strpos($uri, '?')) !== false) {
                $uri    =   substr($uri, 0, $pos);
            }
            $this->uri  =   $uri;
        }
        return $this->uri;
    }
    public function isAjax(){
        return $this->requestMethod == 'ajax';
    }
    public function getEnv($name=null,$default=null){
        if($this->envParams === null){
            $this->envParams    =   new Parameters();
        }
        if($name === null){
            return $this->envParams;
        }
        return $this->envParams->get($name,$default);
    }
    public function getQuery($name=null,$default=''){
        if($this->queryParams === null){
            $this->queryParams    =   new Parameters();
        }
        if($name === null){
            return $this->queryParams;
        }
        return $this->queryParams->get($name,$default);
    }
    public function getPost($name=null,$default=''){
        if($this->postParams === null){
            $this->postParams    =   new Parameters();
        }
        if($name === null){
            return $this->postParams;
        }
        return $this->postParams->get($name,$default);
    }
    public function getFiles($name = null, $default = null)
    {
        if ($this->fileParams === null) {
            $this->fileParams = new Parameters();
        }

        if ($name === null) {
            return $this->fileParams;
        }

        return $this->fileParams->get($name, $default);
    }
    public function getServer($name = null, $default = null)
    {
        if ($this->serverParams === null) {
            $this->serverParams = new Parameters();
        }

        if ($name === null) {
            return $this->serverParams;
        }

        return $this->serverParams->get($name, $default);
    }
    public function queryString($param=array()){
        $query  =   $this->getQuery();
        !empty($param)   && ($query = count($query->toArray())>0 ? array_merge($query->toArray(),$param) : $param);
        return http_build_query($query);
    }
    /**
     * Convert PHP superglobal $_FILES into more sane parameter=value structure
     * This handles form file input with brackets (name=files[])
     *
     * @return array
     */
    protected function mapPhpFiles()
    {
        $files = array();
        foreach ($_FILES as $fileName => $fileParams) {
            $files[$fileName] = array();
            foreach ($fileParams as $param => $data) {
                if (!is_array($data)) {
                    $files[$fileName][$param] = $data;
                } else {
                    foreach ($data as $i => $v) {
                        $this->mapPhpFileParam($files[$fileName], $param, $i, $v);
                    }
                }
            }
            $files[$fileName] = new Parameters($files[$fileName]);
        }

        return $files;
    }

    /**
     * @param array        $array
     * @param string       $paramName
     * @param int|string   $index
     * @param string|array $value
     */
    protected function mapPhpFileParam(&$array, $paramName, $index, $value)
    {
        if (!is_array($value)) {
            $array[$index][$paramName] = $value;
        } else {
            foreach ($value as $i => $v) {
                $this->mapPhpFileParam($array[$index], $paramName, $i, $v);
            }
        }
    }
}
