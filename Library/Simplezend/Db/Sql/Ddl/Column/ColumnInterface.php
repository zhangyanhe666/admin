<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Library\Db\Sql\Ddl\Column;

use Library\Db\Sql\ExpressionInterface;

interface ColumnInterface extends ExpressionInterface
{
    public function getName();
    public function isNullable();
    public function getDefault();
    public function getOptions();
}
