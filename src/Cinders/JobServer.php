<?php
namespace Cinders;

/**
 * Jo bServer
 *
 * @author warmans
 */
class JobServer
{
    const UNIX_SOCKET = '/tmp/cinders_server.sock';

    private $socket;
    private $queues = array();

    public function addJob($queue_name, JobServer\Job $job)
    {
        $this->getQueue($queue_name)->enqueue($job);
    }

    private function getQueue($queue_name)
    {
        if (!array_key_exists($queue_name, $this->queues)) {
            $this->queues[$queue_name] = new \SplQueue();
        }
        return $this->queues[$queue_name];
    }
}
