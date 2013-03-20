<?php
require_once(__DIR__.'/../vendor/autoload.php');

use \Cinders\Ipc;

unlink('/tmp/sub_srv.sock');

$socket = new Ipc\Socket(AF_UNIX, SOCK_STREAM, 0);
$socket->bind('/tmp/sub_srv.sock');
$socket->connect('/tmp/server.sock');

$collection = new Ipc\Socket\Collection();
$collection->attach($socket);

while (1) {

    if(!count($collection)){
        return;
    }

    echo "SELECTING \n";
    $data_to_handle = $collection->select(null);

    if ($data_to_handle['changed'] > 0) {

        foreach ($data_to_handle['read'] as $read_socket) {

            $msg =  $read_socket->read();
            if ($msg === '') {
                $collection->detach($read_socket);
                echo "Server Disconnected\n";
            } else {
                echo 'New Message '.$msg."\n";
            }
        }
    }
}
