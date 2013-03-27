<?php
require_once(__DIR__.'/../vendor/autoload.php');

$worker = new Cinders\JobServer\Client(__DIR__.'/server.sock');
$worker->setLogger(new \Cinders\Log\StdOut());
$worker->sendJob(new \Cinders\JobServer\Job\Sleeper());
