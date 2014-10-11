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

use Bldr\Block\Core\Task\AbstractTask;
use Bldr\Block\Miscellaneous\Service\EnvironmentVariableRepository;
use Bldr\Exception\TaskRuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Luis Cordova   <cordoval@gmail.com>
 * @author Raul Rodriguez <raulrodriguez782@gmail.com>
 * @author Aaron Scherer  <aequasi@gmail.com>
 */
class ExportTask extends AbstractTask
{
    /**
     * @type EnvironmentVariableRepository
     */
    protected $environmentVariableRepository;

    /**
     * @param EnvironmentVariableRepository $environmentVariableRepository
     */
    public function __construct(EnvironmentVariableRepository $environmentVariableRepository)
    {
        $this->environmentVariableRepository = $environmentVariableRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('export')
            ->setDescription('Exports an environmental variable within the context of the bldr task run.')
            ->addParameter('arguments', true, 'Arguments to run on the export', []);
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        foreach ($this->getParameter('arguments') as $argument) {
            if (2 !== count(explode('=', $argument))) {
                throw new TaskRuntimeException(
                    $this->getName(),
                    'Each argument needs to follow the pattern e.g. SYMFONY_ENV=prod'
                );
            };
            $this->environmentVariableRepository->addEnvironmentVariable($argument);
        }
    }
}
