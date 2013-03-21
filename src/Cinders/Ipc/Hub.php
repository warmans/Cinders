<?php
namespace Cinders\Ipc;

/**
 * Hub
 *
 * Acts sort of like a network hub. Any message recieved is just relayed to all connected sockets.
 *
 * @author warmans
 */
class Hub implements \Psr\Log\LoggerAwareInterface
{
    private $logger;
    private $socket;

    /**
     *
     * @param string $listen_socket path to a unix socket
     */
    public function __construct($socket_path)
    {
        $this->socket = new Socket(AF_UNIX, SOCK_STREAM, 0);
        $this->socket->bind($socket_path);
    }

    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function start()
    {
        $this->socket->listen();

        $collection = new Socket\Collection();
        $collection->attach($this->socket);

        while (true) {

            //go into blocking selection
            $this->logger->debug('selecting...');
            $data_to_handle = $collection->select(null);
            $this->logger->debug("{$data_to_handle['changed']} clients changed");

            if ($data_to_handle['changed'] < 1) {
                continue;
            }

            foreach ($data_to_handle['read'] as $active_client) {

                if ($active_client->sameAs($this->socket)) {

                    $new_client = $active_client->accept();

                    if (!$collection->contains($new_client)) {
                        //new client connected
                        $collection->attach($new_client);
                        $this->logger->debug('new client connected: '.print_r($new_client->getPeerinfo(), true));
                    }

                    continue;
                }

                $this->logger->debug($active_client->getPeerinfo(). ' changed');

                //new message or someone disconnected
                if (!($msg = $active_client->read())) {
                    $this->logger->debug('client disconnected: '.print_r($active_client->getPeerinfo(), true));
                    $collection->detach($active_client);
                    continue;
                }

                $this->logger->debug('recieved message: '.$msg->serialise());

                $this->logger->debug(''.count($collection).' clients in collection');

                foreach ($collection as $client) {
                    //send to all clients excluding original sender and myself
                    if (!$client->sameAs($this->socket)) {
                        if (!$client->sameAs($active_client)) {
                            $this->logger->debug('relay sent to '.$client->getPeerinfo());
                            $client->write($msg);
                        } else {
                            $this->logger->debug('relay skipped '.$client->getPeerinfo());
                        }
                    }
                }

            } //endfor
        }//endhile
    }
}
