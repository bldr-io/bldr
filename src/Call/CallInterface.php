<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Call;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
interface CallInterface
{
    /**
     * Runs the command
     *
     * @param array $arguments
     *
     * @return mixed
     */
    public function run(array $arguments);
}
