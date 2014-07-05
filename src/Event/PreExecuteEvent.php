<?php
/**
 * This file is part of Bldr.io
 *
 * (c) Mauricio Walters <nvitius@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Event;

use Bldr\Call\AbstractCall;
use Symfony\Component\Process\ProcessBuilder;

class PreExecuteEvent extends AbstractEvent {
    /**
     * @var AbstractCall
     */
    private $call;
    /**
     * @var ProcessBuilder
     */
    private $builder;

    /**
     * @param AbstractCall   $call
     * @param ProcessBuilder $builder
     * @param bool           $running
     */
    public function __construct(AbstractCall $call, ProcessBuilder $builder, $running = false) {
        parent::__construct($running);
        $this->call = $call;
        $this->builder = $builder;
    }

    public function getCall() {
        return $this->call;
    }

    public function getProcessBuilder() {
        return $this->builder;
    }
}
