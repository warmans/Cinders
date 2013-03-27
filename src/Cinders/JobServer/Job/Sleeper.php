<?php
namespace Cinders\JobServer\Job;

/**
 * Sleeper - used for testing
 *
 * @author warmans
 */
class Sleeper extends \Cinders\JobServer\Job
{
    public function execute()
    {
        sleep(1);
    }
}
