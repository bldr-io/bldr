<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

error_reporting(-1);

if (!file_exists(dirname(__DIR__) . '/composer.lock')) {
    die("Dependencies must be installed using composer:\n\nphp composer.phar install\n\n" .
        "See http://getcomposer.org for help with installing composer\n");
}

$loader = require dirname(__DIR__) . '/vendor/autoload.php';
