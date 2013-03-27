<?php
require_once(__DIR__.'/../vendor/autoload.php');

$server = new \Cinders\JobServer(__DIR__.'/server.sock', new SplQueue());
$server->setLogger(new \Cinders\Log\StdOut());
$server->start();
