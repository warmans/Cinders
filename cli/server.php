<?php
require_once(__DIR__.'/../vendor/autoload.php');

use \Cinders\Ipc;

$sock_path = '/tmp/server.sock';
unlink($sock_path);

$hub = new Ipc\Hub($sock_path);
$hub->setLogger(new \Cinders\Log\StdOut());
$hub->start();