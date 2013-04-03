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
        return $resource;
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
        try {
            $route = new Api\ActiveRoute($request->getPathInfo());

            foreach ($this->resources as $resource) {
                if ($resource->getName() == $route->getCurrentSegment()) {
                    $result = $resource->handleRequest($route, $request);
                    return $this->makeResponse($result['success'], $result['data'], $result['msg']);
                }
            }
        } catch (Api\Exception\Http $e) {
            return $this->makeResponse(false, null, $e->getMessage(), $e->getCode());
        }

        return $this->makeResponse(false, null, 'Resource not found', 404);
    }

    /**
     * Create a Response object
     *
     * @param bool $success
     * @param mixed $data
     * @param string $msg
     * @param int $status
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function makeResponse($success, $data=null, $msg=null, $status=200)
    {
        $response = HttpFoundation\Response::create(
            json_encode(array('success'=>$success,'msg'=>$msg, 'data'=>$data))
        );
        $response->headers->set('content-type', 'application/json');
        $response->setStatusCode($status);
        return $response;
    }
}
