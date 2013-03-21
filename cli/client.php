<?php
require_once(__DIR__.'/../vendor/autoload.php');

use \Cinders\Ipc;

unlink('/tmp/client.sock');

$logger = new \Cinders\Log\StdOut();

$socket = new Ipc\Socket(AF_UNIX, SOCK_STREAM, 0);
$socket->setLogger($logger);
$socket->bind('/tmp/client.sock');
$socket->connect('/tmp/server.sock');

$socket->write(new Ipc\Package(Ipc\Package::TYPE_MESSAGE, 'DO SOMETHING'));
$socket->close();