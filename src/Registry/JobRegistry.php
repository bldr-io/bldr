<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Registry;

use Bldr\Definition\JobDefinition;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class JobRegistry
{
    /**
     * @var JobDefinition[] $jobs
     */
    private $jobs;

    /**
     * @return JobDefinition|null
     */
    public function getNewJob()
    {
        return current($this->jobs);
    }

    /**
     *
     */
    public function markJobComplete()
    {
        array_shift($this->jobs);
    }

    /**
     * @param JobDefinition $job
     *
     * @return $this
     */
    public function addJob(JobDefinition $job)
    {
        $this->jobs[] = $job;

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->jobs);
    }

    /**
     * @return JobDefinition[]
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @param JobDefinition[] $jobs
     *
     * @return $this
     */
    public function setJobs(array $jobs)
    {
        $this->jobs = $jobs;

        return $this;
    }
}
