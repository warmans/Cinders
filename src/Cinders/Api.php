<?php
namespace Cinders;

use \Symfony\Component\HttpFoundation;

class Api {

    /**
     * @var \Cinders\Cinders
     */
    private $cinders;

    private $resources = array();

    public function __construct(Cinders $cinders)
    {
        $this->cinders = $cinders;
    }

    public function addResource(Api\Resource $resource)
    {
        $this->resources[] = $resource;
    }

    public function getResources()
    {
        return $this->resources;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleRequest(HttpFoundation\Request $request)
    {
        $route = new Api\ActiveRoute($request->getPathInfo());

        foreach ($this->resources as $resource) {
            if ($resource->getName() == $route->getCurrentSegment()) {
                return $resource->handleRequest($route, $request);
            }
        }

        return HttpFoundation\Response::create('Resource Not Found', 404);
    }
}
