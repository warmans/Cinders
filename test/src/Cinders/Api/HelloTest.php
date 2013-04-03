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
        $this->object = new \Cinders\Api\Resource\Hello('hello', $this->getMockCinders());
    }

    private function getMockCinders()
    {
        return $this->getMockBuilder('\\Cinders\\Cinders')->disableOriginalConstructor()->getMock();
    }

    /**
     * @group unit-test
     */
    public function testGetName()
    {
        $this->assertEquals('hello', $this->object->getName());
    }

    /**
     * @group unit-test
     */
    public function testGetClaimedPathSegments()
    {
        $this->assertContains('msg', array_keys($this->object->claimPathSegments()));
    }

    /**
     * @group unit-test
     */
    public function testSetGetParent()
    {
        $parent = new \Cinders\Api\Resource\Hello('hello2', $this->getMockCinders());
        $this->object->setParent($parent);
        $this->assertEquals($parent, $this->object->getParent());
    }

    /**
     * @group unit-test
     */
    public function testAddResource()
    {
        $child = new \Cinders\Api\Resource\Hello('hello2', $this->getMockCinders());
        $this->object->addResource($child);
        $this->assertContains($child, $this->object->getSubResources());
    }

    /**
     * @group unit-test
     */
    public function testMatchRoute()
    {
        $route = new \Cinders\Api\ActiveRoute('/hello/world');

        $mock_request = $this->getMock('\\Symfony\\Component\\HttpFoundation\\Request');
        $mock_request
            ->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $result = $this->object->handleRequest($route, $mock_request);

        $this->assertEquals(true, $result['success']);
        $this->assertEquals('world', $result['data']);
    }

    /**
     * @group unit-test
     */
    public function testMatchRouteWithSubResource()
    {
        $route = new \Cinders\Api\ActiveRoute('/hello/world/foo/bar');

        $mock_request = $this->getMock('\\Symfony\\Component\\HttpFoundation\\Request');
        $mock_request
            ->expects($this->atLeastOnce())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $this->object->addResource(new \Cinders\Api\Resource\Hello('foo', $this->getMockCinders()));

        $result = $this->object->handleRequest($route, $mock_request);
        $content = $result['data'];

        $this->assertEquals(true, $result['success']);
        $this->assertEquals('bar', $result['data']);
    }
}
