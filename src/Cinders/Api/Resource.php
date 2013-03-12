<?php
namespace Cinders\Api;

use \Symfony\Component\HttpFoundation;

/**
 * Resource
 *
 * @author Stefan
 */
abstract class Resource
{
    private $parent;
    private $name;
    private $sub_resources = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Allow a resource to claim path segments that occur after it's name
     *
     * @return type
     */
    public function claimPathSegments()
    {
        return array();
    }

    public function setParent(Resource $resource)
    {
        $this->parent = $resource;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addResource(Resource $resource)
    {
        $resource->setParent($this);
        $this->sub_resources[] = $resource;
    }

    public function getSubResources()
    {
        return $this->sub_resources;
    }

    /**
     *
     * @param \Cinders\Api\ActiveRoute $route
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleRequest(ActiveRoute $route, HttpFoundation\Request $request)
    {
        if($route->getCurrentSegment() !== null && $route->getCurrentSegment() != $this->name) {

            //try and match a child resource
            if ($sub_resource = $this->findNamedSubResource($route->getCurrentSegment())) {
                return $sub_resource->handleRequest($route, $request);
            }

            //invalid route - we're probably a child that can't match the current uri
            return HttpFoundation\Response::create('Resource Not Found. Routing failed at: '.$route->getCurrentSegment(), 404);
        }

        //claim any additional arguments
        $resource_arguments = array();
        foreach($this->claimPathSegments() as $name=>$pattern) {
            if(preg_match($pattern, $route->peekNextSegment())){
                $resource_arguments[$name] = $route->getNextSegment();
            } else {
                //if a single segment doesn't match the route is broken
                return HttpFoundation\Response::create('Invalid Resource Arguments', 404);
            }
        }

        //more segments - we'll have to start again
        if($route->peekNextSegment() !== null)
        {
            return $this->handleRequest($route->skipSegment(), $request);
        }

        //route to a handler method
        switch($request->getMethod()){
            case 'GET':
                return $this->handleGet($resource_arguments, $request);
            case 'POST':
                return $this->handleDelete($resource_arguments, $request);
            case 'PUT':
                return $this->handlePut($resource_arguments, $request);
            case 'DELETE':
                return $this->handleDelete($resource_arguments, $request);
            default:
                 return HttpFoundation\Response::create('Unsupported HTTP Method: '.$request->getMethod(), 404);
        }
    }

    private function findNamedSubResource($name)
    {
        foreach($this->sub_resources as $resource)
        {
            if ($name == $resource->getName()) {
                return $resource;
            }
        }
    }

    public function handleGet(array $resource_arguments = array(), HttpFoundation\Request $request)
    {
        return $this->makeResponse(false, null, 'Not Implemented');
    }

    public function handlePost(array $resource_arguments = array(), HttpFoundation\Request $request)
    {
        return $this->makeResponse(false, null, 'Not Implemented');
    }

    public function handlePut(array $resource_arguments = array(), HttpFoundation\Request $request)
    {
        return $this->makeResponse(false, null, 'Not Implemented');
    }

    public function handleDelete(array $resource_arguments = array(), HttpFoundation\Request $request)
    {
        return $this->makeResponse(false, null, 'Not Implemented');
    }

    public function makeResponse($success, $data=null, $msg=null)
    {
        $response = HttpFoundation\Response::create(
            json_encode(array('success'=>$success,'msg'=>$msg, 'data'=>$data))
        );
        $response->headers->set('content-type', 'application/json');
        return $response;
    }
}
