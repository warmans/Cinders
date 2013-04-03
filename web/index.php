<?php
require_once(__DIR__.'/../vendor/autoload.php');

$cinders = new \Cinders\Cinders(__DIR__.'/../usr/projects',  new \Cinders\Filesystem());
//create API
$api = new \Cinders\Api($cinders);

//setup
$api->addResource(new \Cinders\Api\Resource\Hello('hello', $cinders));

$api->addResource(new \Cinders\Api\Resource\Project\Find('projects', $cinders));
$api->addResource(new \Cinders\Api\Resource\Project('project', $cinders));

//handle request
$response = $api->handleRequest(\Symfony\Component\HttpFoundation\Request::createFromGlobals());
$response->send();