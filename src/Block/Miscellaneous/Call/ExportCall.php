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

use Bldr\Block\Miscellaneous\Service\EnvironmentVariableRepository;
use Bldr\Call\AbstractCall;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Raul Rodriguez <raulrodriguez782@gmail.com>
 */
class ExportCall extends AbstractCall
{
    protected $environmentVariableRepository;

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
            ->addOption('env_vars', false, 'Arguments to run on the export', [])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        foreach ($this->getOption('env_vars') as $argument) {
            if (2 !== count(explode('=', $argument))) {
                throw new \RuntimeException('env var needs to follow the pattern e.g. SYMFONY_ENV=prod');
            };
            $this->environmentVariableRepository->addEnvironmentVariable($argument);
        }

        return true;
    }
}
