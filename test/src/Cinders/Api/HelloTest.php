<?php
/**
 * Hello Test
 *
 * @author warmans
 */
namespace Cinders\Api\Resource;

class HelloTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \Cinders\Api\Resource\Hello;
     */
    private $object;

    public function setUp()
    {
        $this->object = new \Cinders\Api\Resource\Hello('hello');
    }

    public function testGetName()
    {
        $this->assertEquals('hello', $this->object->getName());
    }

    public function testGetClaimedPathSegments()
    {
        $this->assertContains('msg', array_keys($this->object->claimPathSegments()));
    }

    public function testSetGetParent()
    {
        $parent = new \Cinders\Api\Resource\Hello('hello2');
        $this->object->setParent($parent);
        $this->assertEquals($parent, $this->object->getParent());
    }

    public function testAddResource()
    {
        $child = new \Cinders\Api\Resource\Hello('hello2');
        $this->object->addResource($child);
        $this->assertContains($child, $this->object->getSubResources());
    }

    public function testMatchRoute()
    {
        $route = new \Cinders\Api\ActiveRoute('/hello/world');

        $mock_request = $this->getMock('\\Symfony\\Component\\HttpFoundation\\Request');
        $mock_request
            ->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $result = $this->object->handleRequest($route, $mock_request);
        $content = json_decode($result->getContent());

        $this->assertEquals(true, $content->success);
        $this->assertEquals('world', $content->data);
    }

    public function testMatchRouteWithSubResource()
    {
        $route = new \Cinders\Api\ActiveRoute('/hello/world/foo/bar');

        $mock_request = $this->getMock('\\Symfony\\Component\\HttpFoundation\\Request');
        $mock_request
            ->expects($this->atLeastOnce())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $this->object->addResource(new \Cinders\Api\Resource\Hello('foo'));

        $result = $this->object->handleRequest($route, $mock_request);
        $content = json_decode($result->getContent());

        $this->assertEquals(true, $content->success);
        $this->assertEquals('bar', $content->data);
    }
}
