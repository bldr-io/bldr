<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Call\Traits;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
trait FinderAwareTrait
{
    /**
     * Finds all the files for the given config
     *
     * @param array $source
     *
     * @return SplFileInfo[]
     *
     * @throws \Exception
     */
    protected function getFiles(array $source)
    {
        $fileSet = [];
        foreach ($source as $set) {
            if (!array_key_exists('files', $set)) {
                throw new \Exception("`src` must have a `files` option");
            }

            if (!array_key_exists('path', $set)) {
                $set['path'] = getcwd();
            }

            if (!array_key_exists('recursive', $set)) {
                $set['recursive'] = false;
            }

            $paths = is_array($set['path']) ? $set['path'] : [$set['path']];
            $files = is_array($set['files']) ? $set['files'] : [$set['files']];
            foreach ($paths as $path) {
                foreach ($files as $file) {
                    $finder = new Finder();
                    $finder->files()->in($path)->name($file);
                    if (!$set['recursive']) {
                        $finder->depth('== 0');
                    }

                    $fileSet = $this->appendFileSet($finder, $fileSet);
                }
            }
        }

        return $fileSet;
    }

    /**
     * @param Finder $finder
     * @param array  $fileSet
     *
     * @return SplFileInfo[]
     */
    protected function appendFileSet(Finder $finder, array $fileSet)
    {
        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $fileSet[] = $file;
        }

        return $fileSet;
    }
}
