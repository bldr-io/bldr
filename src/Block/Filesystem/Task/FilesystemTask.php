<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Filesystem\Task;

use Bldr\Block\Core\Task\AbstractTask;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class FilesystemTask extends AbstractTask
{
    /**
     * @var Filesystem $fileSystem
     */
    protected $fileSystem;

    /**
     * Builds call with an instance of Symfony's Filesystem component
     */
    public function __construct()
    {
        $this->fileSystem = new Filesystem();
    }

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->addParameter('files', true, "Files to run the filesystem command on");
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'filesystem:'.parent::getName();
    }

    /**
     * Returns an array of files or directories for the call
     *
     * @return array
     */
    protected function resolveFiles()
    {
        return $this->getParameter('files');
    }
}
