<?php
namespace Cinders\Api\Resource;

class Hello extends \Cinders\Api\Resource
{
    public function claimPathSegments()
    {
        return array('msg'=>'#[a-z0-9]+#i');
    }

    public function handleGet(array $resource_arguments = array(), \Symfony\Component\HttpFoundation\Request $request)
    {
        return $this->makeResponse(true, $resource_arguments['msg']);
    }
}