<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Miscellaneous\Service;

use Bldr\Event\PreExecuteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Raul Rodriguez <raulrodriguez782@gmail.com>
 */
class EnvironmentVariableSubscriber implements EventSubscriberInterface
{
    protected $environmentVariableRepository;

    public function __construct(EnvironmentVariableRepository $environmentVariableRepository)
    {
        $this->environmentVariableRepository = $environmentVariableRepository;
    }

    /**
     * @param PreExecuteEvent $event
     *
     * @throws \Exception
     */
    public function onPreExecute(PreExecuteEvent $event)
    {
        $builder = $event->getProcessBuilder();
        foreach ($this->environmentVariableRepository->getEnvironmentVariables() as $row) {
            list ($key, $value) = explode('=', $row);
            $builder->setEnv($key, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'bldr.event.execute.before' => [
                ['onPreExecute', 0]
            ]
        ];
    }
}
