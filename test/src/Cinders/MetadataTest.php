<?php
/**
 * @author warmans
 */
namespace Cinders;

class MetadataTest extends \PHPUnit_Framework_TestCase {

    private $mock_file;

    public function setUp()
    {
        $this->mock_file = $this->getMockFileObject();
    }

    public function getMockFileObject()
    {
        return $this->getMock('\SplTempFileObject');
    }

    /**
     * @covers \Cinders\Metadata::__construct
     * @covers \Cinders\Metadata::__get
     * @covers \Cinders\Metadata::reloadFromDisk
     * @covers \Cinders\Metadata::decodeData
     */
    public function testLoadValidFile()
    {
        $this->mock_file
            ->expects($this->once())
            ->method('flock')
            ->with($this->equalTo(LOCK_EX))
            ->will($this->returnValue(true));

        $this->mock_file
            ->expects($this->atLeastOnce())
            ->method('valid')
            ->will($this->onConsecutiveCalls($this->returnValue(true), $this->returnValue(false)));

        $this->mock_file
            ->expects($this->atLeastOnce())
            ->method('current')
            ->will($this->onConsecutiveCalls($this->returnValue('{"foo":"bar"}'), $this->returnValue(null)));

        $object = new \Cinders\Metadata($this->mock_file);

        $this->assertEquals('bar', $object->foo);
    }

    public function testSetData()
    {
        $this->mock_file
            ->expects($this->once())
            ->method('flock')
            ->with($this->equalTo(LOCK_EX))
            ->will($this->returnValue(true));

        $this->mock_file
            ->expects($this->once())
            ->method('ftruncate');

        $this->mock_file
            ->expects($this->once())
            ->method('fwrite')
            ->with($this->equalTo('{"cat":"dog"}'))
            ->will($this->returnValue(14));

        $object = new \Cinders\Metadata($this->mock_file);
        $object->setData(array('cat'=>'dog'));
    }
}
