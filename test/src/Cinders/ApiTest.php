<?php
/**
 * Server Test
 *
 * @author warmans
 */
namespace Cinders;

class ApiTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \Cinders\Api;
     */
    private $object;

    public function setUp()
    {
        $this->object = new \Cinders\Api($this->getMockCinders());
    }

    private function getMockCinders()
    {
        return $this->getMockBuilder('\\Cinders\\Cinders')->disableOriginalConstructor()->getMock();
    }

    /**
     * @group unit-test
     */
    public function testAddResource()
    {
        $resource = new \Cinders\Api\Resource\Hello('hello');
        $this->object->addResource($resource);
        $this->assertEquals(array($resource), $this->object->getResources());
    }

    /**
     * @group unit-test
     */
    public function testHandleRequest()
    {
        $this->object->addResource(new Api\Resource\Hello('hello'));

        $mock_request = $this->getMock('\\Symfony\\Component\\HttpFoundation\\Request');
        $mock_request
            ->expects($this->atLeastOnce())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $mock_request
            ->expects($this->atLeastOnce())
            ->method('getPathInfo')
            ->will($this->returnValue('/hello/world'));

        $response = $this->object->handleRequest($mock_request);
        $result = json_decode($response->getContent());

        $this->assertTrue($result->success);
        $this->assertEquals('world', $result->data);
    }
}
