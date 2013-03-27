<?php
namespace Cinders\JobServer;

/**
 * Abstract Job
 *
 * @author warmans
 */
abstract class Job
{
    abstract public function execute();
}
