<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Database\Service\Mysql;

use Bldr\Extension\Database\Service\AbstractMysqlService;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class CreateUserService extends AbstractMysqlService
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        parent::configure();

        $this->setName('mysql:user')
            ->setDescription('Creates a mysql user')
            ->addOption('new_username', true, 'Username to create')
            ->addOption('new_password', false, 'Password for the new user')
            ->addOption('allowed_host', true, 'Allowed Host for the new user (localhost)', 'localhost')
            ->addOption('privileges', true, 'Privileges to grant the new user')
            ->addOption('table', true, 'Table to grant the new privileges on');
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $file = sys_get_temp_dir() . '/bldr_mysql_' . microtime(true) . '.sql';

        $database = $this->hasOption('database') ? '--database=' . $this->getOption('database') : '';

        $arguments = [
            '--user='.$this->getOption('username'),
            '--password='.$this->getOption('password')
        ];

        if ($this->hasOption('database')) {
            $arguments[] = '--database'.$this->getOption('database');
        }
        if ($this->getOption('host') !== '127.0.0.1') {
            $arguments[] = '--host='.$this->getOption('host');
        }
        if ($this->getOption('port') !== 3306) {
            $arguments[] = '--port='.$this->getOption('port');
        }
        $arguments[] = '<';
        $arguments[] = $file;


        $sql = sprintf(
            'GRANT %s ON %s to \'%s\'@\'%s\' IDENTIFIED BY \'%s\'',
            implode(', ', $this->getOption('privileges')),
            $this->getOption('table'),
            $this->getOption('new_username'),
            $this->getOption('allowed_host'),
            $this->getOption('password')
        );

        file_put_contents($file, $sql);

        $this->setOption('arguments', $arguments);
        parent::run();
    }
}