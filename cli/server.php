<?php
require_once(__DIR__.'/../vendor/autoload.php');

use \Cinders\Ipc;

unlink('/tmp/server.sock');

$socket = new Ipc\Socket(AF_UNIX, SOCK_STREAM, 0);
$socket->bind('/tmp/server.sock');
$socket->listen();

$collection = new Ipc\Socket\Collection();
$collection->attach($socket);

while(1){
    echo time()." SELECTING \n";

    $data_to_handle = $collection->select(null);
    echo "{$data_to_handle['changed']} CHANGED \n";

    if ($data_to_handle['changed'] > 0) {

        foreach ($data_to_handle['read'] as $reader) {

            if($reader->sameAs($socket)){
                echo "Hello new client \n";
                $collection->attach($socket->accept());
            } else {

                if (!$reader->isConnected()) {
                    echo "Bye client \n";
                    $collection->detach($reader);
                }

                $msg = $reader->read();

                foreach ($collection as $client) {
                    if (!$client->sameAs($socket) && !$client->sameAs($reader)) {
                        echo "Relaying message \n";
                        $client->write($msg);
                    }
                }
            }
        }
    }
}
