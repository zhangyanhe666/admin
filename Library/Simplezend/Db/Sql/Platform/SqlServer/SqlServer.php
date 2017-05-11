<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Library\Db\Sql\Platform\SqlServer;

use Library\Db\Sql\Platform\AbstractPlatform;

class SqlServer extends AbstractPlatform
{

    public function __construct(SelectDecorator $selectDecorator = null)
    {
        $this->setTypeDecorator('Library\Db\Sql\Select', ($selectDecorator) ?: new SelectDecorator());
    }
}
