<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Library\Db\Sql\Platform\Mysql;

use Library\Db\Adapter\AdapterInterface;
use Library\Db\Adapter\Driver\DriverInterface;
use Library\Db\Adapter\ParameterContainer;
use Library\Db\Adapter\Platform\PlatformInterface;
use Library\Db\Adapter\StatementContainerInterface;
use Library\Db\Sql\Platform\PlatformDecoratorInterface;
use Library\Db\Sql\Select;

class SelectDecorator extends Select implements PlatformDecoratorInterface
{
    /**
     * @var Select
     */
    protected $select = null;

    /**
     * @param Select $select
     */
    public function setSubject($select)
    {
        $this->select = $select;
    }

    /**
     * @param AdapterInterface $adapter
     * @param StatementContainerInterface $statementContainer
     */
    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer)
    {
        // localize variables
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }
        parent::prepareStatement($adapter, $statementContainer);
    }

    /**
     * @param PlatformInterface $platform
     * @return string
     */
    public function getSqlString(PlatformInterface $platform = null)
    {
        // localize variables
        foreach (get_object_vars($this->select) as $name => $value) {
            $this->{$name} = $value;
        }
        return parent::getSqlString($platform);
    }

    protected function processLimit(PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null)
    {
        if ($this->limit === null) {
            return null;
        }
        if ($driver) {
            $sql = $driver->formatParameterName('limit');
            $parameterContainer->offsetSet('limit', (int) $this->limit, ParameterContainer::TYPE_INTEGER);
        } else {
            $sql = $this->limit;
        }

        return array($sql);
    }

    protected function processOffset(PlatformInterface $platform, DriverInterface $driver = null, ParameterContainer $parameterContainer = null)
    {
        if ($this->offset === null) {
            return null;
        }
        if ($driver) {
            $parameterContainer->offsetSet('offset', (int) $this->offset, ParameterContainer::TYPE_INTEGER);
            return array($driver->formatParameterName('offset'));
        }

        return array($this->offset);
    }
}
