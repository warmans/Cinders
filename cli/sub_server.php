<?php
require_once(__DIR__.'/../vendor/autoload.php');

use \Cinders\Ipc;

unlink('/tmp/sub_srv.sock');

$logger = new \Cinders\Log\StdOut();

$socket = new Ipc\Socket(AF_UNIX, SOCK_STREAM, 0);
$socket->setLogger($logger);
$socket->bind('/tmp/sub_srv.sock');
$socket->connect('/tmp/server.sock');

$collection = new Ipc\Socket\Collection();
$collection->attach($socket);

while (1) {

    if (!count($collection)) {
        return;
    }

    echo "SELECTING \n";
    $data_to_handle = $collection->select(null);

    if ($data_to_handle['changed'] > 0) {

        foreach ($data_to_handle['read'] as $read_socket) {

            $msg =  $read_socket->read();
            if (false === $msg) {
                $collection->detach($read_socket);
                echo "Server Disconnected\n";
            } else {

                echo 'New Message '.$msg->getPayload()."\n";

                if ($msg->getType() != Ipc\Package::TYPE_ACK) {
                    $read_socket->write(new Ipc\Package(Ipc\Package::TYPE_ACK, 'OK'));
                }
            }
        }
    }
}
