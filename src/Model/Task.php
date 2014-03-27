<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Model;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Task
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var Call[] $calls
     */
    private $calls = [];

    /**
     * @param string $name
     * @param string $description
     * @param Call[] $calls
     */
    public function __construct($name, $description = '', array $calls = [])
    {
        $this->name  = $name;
        $this->description = $description;
        if (sizeof($calls) > 0) {
            foreach ($calls as $data) {
                if (is_array($data)) {
                    $call = new Call($data['type']);
                    unset($data['type']);
                    $call->setOptions($data);
                } else {
                    $call = $data;
                }

                $this->addCall($call);
            }
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Call[]
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * @param Call $call
     */
    public function addCall(Call $call)
    {
        $this->calls[] = $call;
    }
}
