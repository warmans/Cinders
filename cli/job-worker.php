<?php
require_once(__DIR__.'/../vendor/autoload.php');

$worker = new Cinders\JobServer\Worker(__DIR__.'/server.sock');
$worker->setLogger(new \Cinders\Log\StdOut());
$worker->work();
