<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Library\Db\TableGateway;

use Library\Db\Adapter\AdapterInterface;
use Library\Db\ResultSet\ResultSet;
use Library\Db\ResultSet\ResultSetInterface;
use Library\Db\Sql\Delete;
use Library\Db\Sql\Insert;
use Library\Db\Sql\Select;
use Library\Db\Sql\Sql;
use Library\Db\Sql\TableIdentifier;
use Library\Db\Sql\Update;
use Library\Db\Sql\Where;
use Library\Db\Adapter\Adapter;

/**
 *
 * @property AdapterInterface $adapter
 * @property int $lastInsertValue
 * @property string $table
 */
abstract class MyAbstractTableGateway implements TableGatewayInterface
{

    /**
     * @var AdapterInterface
     */
    protected $adapter = null;

    /**
     * @var string
     */
    protected $table = null;

    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @var Feature\FeatureSet
     */
    protected $featureSet = null;

    /**
     * @var ResultSetInterface
     */
    protected $resultSetPrototype = null;

    /**
     * @var Sql
     */
    protected $sql = null;

    /**
     *
     * @var int
     */
    protected $lastInsertValue = null;

    abstract function init();
    public function setTable($table){
        $this->table    =   $table;
        return $this;
    }
    /**
     * Get table name
     *
     * @return string
     */
    public function getTable()
    {
        if (!is_string($this->table) && !$this->table instanceof TableIdentifier) {
            throw new Exception\RuntimeException('This table object does not have a valid table set.');
        }
        return $this->table;
    }

    public function setAdapterByConfig($dbConfig){
        $this->adapter  =    Adapter::getInstance($dbConfig);//new Adapter($dbConfig);
        $this->sql      =   NULL;
        return $this;
    }
    
    public function getDbName($dbConfig){
        preg_match('/dbname=(.+)\;/',$dbConfig['dsn'],$matches);
        $dbname    =   $matches[1];
        return $dbname;
    }
    /**
     * Get adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        if (!$this->adapter instanceof AdapterInterface) {
            throw new Exception\RuntimeException('This table does not have an Adapter setup');
        }
        return $this->adapter;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return Feature\FeatureSet
     */
    public function getFeatureSet()
    {
        if (!$this->featureSet instanceof Feature\FeatureSet) {
            $this->featureSet = new Feature\FeatureSet;
            $this->featureSet->setTableGateway($this);
            $this->featureSet->apply('preInitialize', array());
        }
        return $this->featureSet;
    }

    /**
     * Get select result prototype
     *
     * @return ResultSet
     */
    public function getResultSetPrototype()
    {
        if (!$this->resultSetPrototype instanceof ResultSetInterface) {
            $this->resultSetPrototype = new ResultSet;
        }
        return $this->resultSetPrototype;
    }

    /**
     * @return Sql
     */
    public function getSql()
    {
         if (!$this->sql instanceof Sql) {
            $this->sql = new Sql($this->getAdapter());
        }
        $this->sql->setTable($this->getTable());
        return $this->sql;
    }

    /**
     * Select
     *
     * @param Where|\Closure|string|array $where
     * @return ResultSet
     */
    public function select($where = null)
    {
        $select = $this->getSql()->select();

        if ($where instanceof \Closure) {
            $where($select);
        } elseif ($where !== null) {
            $select->where($where);
        }

        return $this->selectWith($select);
    }

    /**
     * @param Select $select
     * @return null|ResultSetInterface
     * @throws \RuntimeException
     */
    public function selectWith(Select $select)
    {
        return $this->executeSelect($select);
    }

    /**
     * @param Select $select
     * @return ResultSet
     * @throws Exception\RuntimeException
     */
    protected function executeSelect(Select $select)
    {
        $selectState = $select->getRawState();
        if ($selectState['table'] != $this->getTable()) {
            throw new Exception\RuntimeException('The table name of the provided select object must match that of the table');
        }

        if ($selectState['columns'] == array(Select::SQL_STAR)
            && $this->columns !== array()) {
            $select->columns($this->columns);
        }

        // apply preSelect features
        $this->getFeatureSet()->apply('preSelect', array($select));

        // prepare and execute
        $statement = $this->getSql()->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        // build result set
        $resultSet = clone $this->getResultSetPrototype();
        $resultSet->initialize($result);

        // apply postSelect features
        $this->getFeatureSet()->apply('postSelect', array($statement, $result, $resultSet));

        return $resultSet;
    }

    /**
     * Insert
     *
     * @param  array $set
     * @return int
     */
    public function insert($set)
    {
        $insert = $this->getSql()->insert();
        $insert->values($set);
        return $this->executeInsert($insert);
    }

    /**
     * @param Insert $insert
     * @return mixed
     */
    public function insertWith(Insert $insert)
    {
        return $this->executeInsert($insert);
    }

    /**
     * @todo add $columns support
     *
     * @param Insert $insert
     * @return mixed
     * @throws Exception\RuntimeException
     */
    protected function executeInsert(Insert $insert)
    {
        $insertState = $insert->getRawState();
        if ($insertState['table'] != $this->getTable()) {
            throw new Exception\RuntimeException('The table name of the provided Insert object must match that of the table');
        }

        // apply preInsert features
        $this->getFeatureSet()->apply('preInsert', array($insert));
        $this->adminLog($this->getSql()->getSqlStringForSqlObject($insert));
        $statement = $this->getSql()->prepareStatementForSqlObject($insert);
        $result = $statement->execute();
        $this->lastInsertValue = $this->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();

        // apply postInsert features
         $this->getFeatureSet()->apply('postInsert', array($statement, $result));

        return $result->getAffectedRows();
    }

    /**
     * Update
     *
     * @param  array $set
     * @param  string|array|closure $where
     * @return int
     */
    public function update($set, $where = null,$limit=null)
    {
        $update = $this->getSql()->update();
        $update->set($set);
        if ($where !== null) {
            $update->where($where);
        }
        if ($limit !== null) {
            $update->limit($limit);
        }
        return $this->executeUpdate($update);
    }

    /**
     * @param \Library\Db\Sql\Update $update
     * @return mixed
     */
    public function updateWith(Update $update)
    {
        return $this->executeUpdate($update);
    }

    /**
     * @todo add $columns support
     *
     * @param Update $update
     * @return mixed
     * @throws Exception\RuntimeException
     */
    protected function executeUpdate(Update $update)
    {
        $updateState = $update->getRawState();
        if ($updateState['table'] != $this->getTable()) {
            throw new Exception\RuntimeException('The table name of the provided Update object must match that of the table');
        }

        // apply preUpdate features
        $this->getFeatureSet()->apply('preUpdate', array($update));

        $this->adminLog($this->getSql()->getSqlStringForSqlObject($update));
        $statement = $this->getSql()->prepareStatementForSqlObject($update);
        $result = $statement->execute();

        // apply postUpdate features
        $this->getFeatureSet()->apply('postUpdate', array($statement, $result));

        return $result->getAffectedRows();
    }

    /**
     * Delete
     *
     * @param  Where|\Closure|string|array $where
     * @return int
     */
    public function delete($where)
    {
        $delete = $this->getSql()->delete();
        if ($where instanceof \Closure) {
            $where($delete);
        } else {
            $delete->where($where);
        }
        return $this->executeDelete($delete);
    }

    /**
     * @param Delete $delete
     * @return mixed
     */
    public function deleteWith(Delete $delete)
    {
        return $this->executeDelete($delete);
    }

    /**
     * @todo add $columns support
     *
     * @param Delete $delete
     * @return mixed
     * @throws Exception\RuntimeException
     */
    protected function executeDelete(Delete $delete)
    {
        $deleteState = $delete->getRawState();
        if ($deleteState['table'] != $this->getTable()) {
            throw new Exception\RuntimeException('The table name of the provided Update object must match that of the table');
        }

        // pre delete update
        $this->getFeatureSet()->apply('preDelete', array($delete));
        $this->adminLog($this->getSql()->getSqlStringForSqlObject($delete));
        $statement = $this->getSql()->prepareStatementForSqlObject($delete);
        $result = $statement->execute();

        // apply postDelete features
        $this->getFeatureSet()->apply('postDelete', array($statement, $result));

        return $result->getAffectedRows();
    }

    /**
     * Get last insert value
     *
     * @return int
     */
    public function getLastInsertValue()
    {
        return $this->lastInsertValue;
    }

    /**
     * __get
     *
     * @param  string $property
     * @throws Exception\InvalidArgumentException
     * @return mixed
     */
    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'lastinsertvalue':
                return $this->lastInsertValue;
            case 'adapter':
                return $this->adapter;
            case 'table':
                return $this->table;
        }
        if ($this->getFeatureSet()->canCallMagicGet($property)) {
            return $this->getFeatureSet()->callMagicGet($property);
        }
        throw new Exception\InvalidArgumentException('Invalid magic property access in ' . __CLASS__ . '::__get()');
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return mixed
     * @throws Exception\InvalidArgumentException
     */
    public function __set($property, $value)
    {
        if ($this->getFeatureSet()->canCallMagicSet($property)) {
            return $this->getFeatureSet()->callMagicSet($property, $value);
        }
        throw new Exception\InvalidArgumentException('Invalid magic property access in ' . __CLASS__ . '::__set()');
    }
    public function adminLog($sql){
        return;
    }
    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws Exception\InvalidArgumentException
     */
    public function __call($method, $arguments)
    {
        if ($this->getFeatureSet()->canCallMagicCall($method)) {
            return $this->getFeatureSet()->callMagicCall($method, $arguments);
        }
        throw new Exception\InvalidArgumentException('Invalid method (' . $method . ') called, caught by ' . __CLASS__ . '::__call()');
    }

    /**
     * __clone
     */
    public function __clone()
    {
        $this->resultSetPrototype = (isset($this->resultSetPrototype)) ? clone $this->resultSetPrototype : null;
        $this->sql = clone $this->sql;
        if (is_object($this->table)) {
            $this->table = clone $this->table;
        }
    }

}
