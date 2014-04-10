<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Miscellaneous\Call;

use Bldr\Extension\Execute\Call\ExecuteCall;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ServiceCall extends ExecuteCall
{
    private static $MANAGERS = ['service', 'init.d', 'launchctl'];

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('service')
            ->setDescription('Used to stop/start/restart services')
            ->addOption(
                'manager',
                true,
                sprintf(
                    'Method used to manage service: %s',
                    implode(', ', static::$MANAGERS)
                ),
                'service'
            )
            ->addOption('service', true, 'Service to manage')
            ->addOption('method', true, 'Method to run on the service manager. <restart>', 'restart')
            ->addOption('dry_run', true, 'If set, will not run command', false);
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function run()
    {
        $this->addOption('executable', true)
            ->addOption('arguments', true);

        $arguments  = [];
        $manager = $this->getOption('manager');
        switch ($manager) {
            case 'service':
                $executable  = 'sudo';
                $arguments[] = $manager;
                $arguments[] = $this->getOption('service');
                $arguments[] = $this->getOption('method');
                break;
            case 'init.d':
                $executable  = 'sudo';
                $arguments[] = '/etc/init.d/'.$this->getOption('service');
                $arguments[] = $this->getOption('method');
                break;
            case 'launchctl':
                $executable  = $manager;
                $arguments[] = $this->getOption('method');
                $arguments[] = $this->getOption('service');
                break;
            default:
                throw new \Exception(
                    $manager.' is not a valid manager for services. Feel free to '.
                    'create a pull request, if you think it should be.'
                );
        }

        $this->setOption('executable', $executable);
        $this->setOption('arguments', $arguments);

        return parent::run();
    }
}
