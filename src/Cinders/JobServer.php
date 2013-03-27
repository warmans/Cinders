<?php
namespace Cinders;

/**
 * Jo bServer
 *
 * @author warmans
 */
class JobServer extends Ipc\AbstractServer
{
    const MAX_WORKERS = 2;

    /**
     * @var \SplQueue
     */
    private $queue;

    public function __construct($socket_path, \SplQueue $queue)
    {
        parent::__construct($socket_path);
        $this->queue = $queue;
    }

    public function start()
    {
        for ($i=0; $i < self::MAX_WORKERS; $i++) {
            passthru('nohup php job-worker.php >> /tmp/workers.log 2>&1 &', $status);
        }

        parent::start();
        $this->listen_socket->setOption(SOL_SOCKET, SO_REUSEADDR);
    }

    /**
     *
     * @param \Cinders\Ipc\Socket $active_socket
     * @param array $msgs
     */
    public function handleMsgs($active_socket, $msgs = array())
    {
        foreach ($msgs as $msg) {

            switch ($msg->getType()) {

                case (JobServer\Package::TYPE_JOB):
                    $this->queue->enqueue($msg->getPayload());
                    break;

                case (JobServer\Package::TYPE_JOB_REQUEST):
                    $this->handleJobRequest($active_socket);
                    break;

                default:
                    //other
                    break;
            }
        }
    }

    /**
     * @param \Cinders\Ipc\Socket $requester
     */
    protected function handleJobRequest($requester)
    {
        if($this->queue->count() > 0){
            if ($job = $this->queue->shift()) {

                //send job
                if (!$requester->write(new JobServer\Package(JobServer\Package::TYPE_JOB, $job))) {
                    //something went wrong - re-queue job
                    $this->queue->enqueue($job);
                }
            }
        }
    }
}
