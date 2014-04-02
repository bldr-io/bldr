<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Database\Service;

use Bldr\Call\AbstractCall;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class AbstractMysqlService extends AbstractCall
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        parent::configure();

        $this->addOption('username', true, 'Username for the mysql CLI')
            ->addOption('password', true, 'Password for the mysql cli')
            ->addOption('host', true, 'IP/Host of the mysql server (127.0.0.1)', '127.0.0.1')
            ->addOption('port', true, 'Port of the mysql server (3306)', 3306)
            ->addOption('database', false, 'Database to use for the mysql cli');
    }
}
