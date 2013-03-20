<?php
require_once(__DIR__.'/../vendor/autoload.php');

use \Cinders\Ipc;

unlink('/tmp/client.sock');

$socket = new Ipc\Socket(AF_UNIX, SOCK_STREAM, 0);
$socket->bind('/tmp/client.sock');
$socket->connect('/tmp/server.sock');

$socket->write('DO SOMETHING');