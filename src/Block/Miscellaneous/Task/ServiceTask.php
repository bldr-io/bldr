<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Miscellaneous\Task;

use Bldr\Block\Execute\Task\ExecuteTask;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ServiceTask extends ExecuteTask
{
    private static $MANAGERS = ['service', 'init.d', 'launchctl'];

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('service')
            ->setDescription('Used to stop/start/restart services')
            ->addParameter(
                'manager',
                true,
                sprintf(
                    'Method used to manage service: %s',
                    implode(', ', static::$MANAGERS)
                ),
                'service'
            )
            ->addParameter('service', true, 'Service to manage')
            ->addParameter('method', true, 'Method to run on the service manager. <restart>', 'restart')
            ->addParameter('sudo', true, 'Run as sudo?', true)
            ->addParameter('dry_run', true, 'If set, will not run command', false)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        $arguments  = [];
        $sudo       = $this->getParameter('sudo');
        $manager    = $this->getParameter('manager');
        switch ($manager) {
            case 'service':
                $executable = $manager;
                if ($sudo) {
                    $executable  = 'sudo';
                    $arguments[] = $manager;
                }
                $arguments[] = $this->getParameter('service');
                $arguments[] = $this->getParameter('method');
                break;
            case 'init.d':
                $executable = '/etc/init.d/'.$this->getParameter('service');
                if ($sudo) {
                    $executable  = 'sudo';
                    $arguments[] = $manager;
                }
                $arguments[] = $this->getParameter('method');
                break;
            case 'launchctl':
                $executable = $manager;
                if ($sudo) {
                    $executable  = 'sudo';
                    $arguments[] = $manager;
                }
                $arguments[] = $this->getParameter('method');
                $arguments[] = $this->getParameter('service');
                break;
            default:
                throw new \Exception(
                    $manager.' is not a valid manager for services. Feel free to '.
                    'create a pull request, if you think it should be.'
                );
        }

        $this->addParameter('executable', true)
            ->setParameter('executable', $executable)
            ->addParameter('arguments', true)
            ->setParameter('arguments', $arguments);

        parent::run($output);
    }
}
