<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
use Application\Base\Controller;
class InstallController extends Controller
{   
    public function init() {
        if($this->getMyGlobal()->isInstall()){
            $this->router()->toUrl($this->getMyGlobal()->index);
        }
    }

    public function indexAction()
    {        
        $this->setVariable('myGlobal', $this->getMyGlobal());
        $this->setPTpl('install');
    }
    public function installdbAction(){   
        $dsn        =   $this->getRequest()->getPost('dsn');
        $username   =   $this->getRequest()->getPost('username');
        $password   =   $this->getRequest()->getPost('password');
        $dbname     =   $this->getRequest()->getPost('dbname');
        try{
            //创建数据库
            $this->createDatabase($dsn,$username,$password,$dbname);
            //创建表
            $this->createTable($dsn,$username,$password,$dbname);
            //创建local配置文件
            $this->createConfigLocal($dsn,$username,$password,$dbname);
        } catch (\Exception $exc) {
            return $this->defaultJson('N',  \Application\Tool\Comment::getSqlError($exc));
        }        
        
        return $this->defaultJson();
    }
    //创建数据库
    private function createDatabase($dsn,$username,$password,$dbname){
        $sys        =   $this->getSysTable(str_replace('%s','',$dsn),$username,$password);
        $sql        =   'SELECT count(*)  num FROM INFORMATION_SCHEMA.TABLES WHERE table_schema="'.$dbname.'"';//检测数据库是否存在
        $res        =   $sys->query($sql, 'execute');
        if($res->current()->num > 0){
            throw new \Exception('数据库已存在');
        }else{
            $createSql  =   'create database '.$dbname.';';
            $sys->query($createSql, 'execute');
        }
    }
    //创建表
    private function createTable($dsn,$username,$password,$dbname){
        $install    =   new \Library\Application\File($this->router()->projectPath('Config/install.sql'));
        $installsql =   $install->get();
        $sys        =   $this->getSysTable(str_replace('%s',$dbname,$dsn),$username,$password);
        $sys->query('use '.$dbname,'execute');
        if(!$sys->query($installsql,'execute')){
            throw new \Exception('创建系统表失败，请查看model/Application/config/install.sql中的sql是否有错误');
        }
    }
    //创建local配置文件
    private function createConfigLocal($dsn, $username, $password,$dbname){
        $this->getServer('config')->add('sys',str_replace('%s',$dbname,$dsn), $username, $password);
    }  
    //获取数据库对象
    private function getSysTable($dsn,$username,$password){        
        $config     =   $this->configFormat($dsn, $username, $password);
        $sysTable   =   new \Library\Db\Adapter\Adapter($config['sys']);
        return $sysTable;
    }
    private function configFormat($dsn, $username, $password){
            $data    =    array(
                        'sys' => array(
                                        'driver'            => 'Pdo',
                                        'key'               => 'sys',
                                        'dsn'               => $dsn,
                                        'username'          => $username,
                                        'password'          => $password,
                                        'driver_options'    => array(
                                            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                                        )
                        ),                         
                );
            return $data;
    }

}
