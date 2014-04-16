<?php

/**
 * This file is part of Bldr.io.
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

set_time_limit(0);

if (function_exists('ini_set')) {
    @ini_set('display_errors', 1);

    $memoryInBytes = function ($value) {
        $unit = strtolower(substr($value, -1, 1));
        $value = (int) $value;
        switch($unit) {
            case 'g':
                $value *= 1024;
            // no break (cumulative multiplier)
            case 'm':
                $value *= 1024;
            // no break (cumulative multiplier)
            case 'k':
                $value *= 1024;
        }

        return $value;
    };

    $memoryLimit = trim(ini_get('memory_limit'));
    // Increase memory_limit if it is lower than 512M
    if ($memoryLimit != -1 && $memoryInBytes($memoryLimit) < 512 * 1024 * 1024) {
        @ini_set('memory_limit', '512M');
    }
    unset($memoryInBytes, $memoryLimit);
}

use Dflydev\EmbeddedComposer\Core\EmbeddedComposerBuilder;
use Symfony\Component\Console\Input\ArgvInput;

$input = new ArgvInput;

if ($projectDir = $input->getParameterOption('--project-dir')) {
    if (false !== strpos($projectDir, '~') && function_exists('posix_getuid')) {
        $info = posix_getpwuid(posix_getuid());
        $projectDir = str_replace('~', $info['dir'], $projectDir);
    }

    if (! is_dir($projectDir)) {
        throw new \InvalidArgumentException(
            sprintf("Specified project directory %s does not exist", $projectDir)
        );
    }

    chdir($projectDir);
}

$embeddedComposerBuilder = new EmbeddedComposerBuilder($classLoader);

$embeddedComposer = $embeddedComposerBuilder
    ->setComposerFilename('bldr.json')
    ->setVendorDirectory('.bldr/vendor/')
    ->build();

$embeddedComposer->processAdditionalAutoloads();

Bldr\Application::create($embeddedComposer);
