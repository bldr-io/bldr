<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Miscellaneous\Call;

use Bldr\Block\Execute\Call\ExecuteCall;

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
            ->addOption('sudo', true, 'Run as sudo?', false)
            ->addOption('dry_run', true, 'If set, will not run command', false)
        ;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function run()
    {
        $this->addOption('executable', true)
            ->addOption('arguments', true)
        ;

        $arguments  = [];
        $sudo       = $this->getOption('sudo');
        $manager    = $this->getOption('manager');
        switch ($manager) {
            case 'service':
                $executable = $manager;
                if ($sudo) {
                    $executable  = 'sudo';
                    $arguments[] = $manager;
                }
                $arguments[] = $this->getOption('service');
                $arguments[] = $this->getOption('method');
                break;
            case 'init.d':
                $executable = '/etc/init.d/'.$this->getOption('service');
                if ($sudo) {
                    $executable  = 'sudo';
                    $arguments[] = $manager;
                }
                $arguments[] = $this->getOption('method');
                break;
            case 'launchctl':
                $executable = $manager;
                if ($sudo) {
                    $executable  = 'sudo';
                    $arguments[] = $manager;
                }
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
