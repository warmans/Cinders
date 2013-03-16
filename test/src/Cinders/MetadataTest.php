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
     * @group unit-test
     * @covers \Cinders\Metadata::__construct
     * @covers \Cinders\Metadata::__get
     * @covers \Cinders\Metadata::reloadFromDisk
     * @covers \Cinders\Metadata::decodeData
     */
    public function testLoadValidFile()
    {
        $this->mock_file
            ->expects($this->atLeastOnce())
            ->method('flock')
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

    /**
     * @group unit-test
     */
    public function testSetData()
    {
        $this->mock_file
            ->expects($this->any())
            ->method('flock')
            ->will($this->returnValue(true));

        $this->mock_file
            ->expects($this->any())
            ->method('fwrite')
            ->will($this->returnValue(100));

        $object = new \Cinders\Metadata($this->mock_file);

        $object->setData(array('foo'=>array('bar'=>'baz')));
        $this->assertEquals('baz', $object->foo->bar);

        $object->setData(array('foo'=>array('cat'=>'dog')));
        $this->assertEquals('baz', $object->foo->bar); //old value still exists
        $this->assertEquals('dog', $object->foo->cat); //new value was merged in

        $object->setData(array('foo'=>'bart'));
        $this->assertEquals('bart', $object->foo); //foo obj replaced with scalar value
        $this->assertFalse(isset($object->foo->cat)); //cat no longer exists

        $object->setData(array('foo'=>'bort'));
        $this->assertEquals('bort', $object->foo); //bart val replaced with bort val

        //deep recursion test
        $object->setData(array('foo'=>array('homer'=>array('marge'=>array('lisa'=>'1')))));
        $object->setData(array('foo'=>array('homer'=>array('marge'=>array('bart'=>'2')))));
        $object->setData(array('foo'=>array('homer'=>array('marge'=>array('maggie'=>'3')))));
        $this->assertEquals('1', $object->foo->homer->marge->lisa);
        $this->assertEquals('2', $object->foo->homer->marge->bart);
        $this->assertEquals('3', $object->foo->homer->marge->maggie);
    }

    /**
     * @group unit-test
     */
    public function testSetDataWritesToDisk()
    {
        $this->mock_file
            ->expects($this->atLeastOnce())
            ->method('flock')
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

    /**
     * @group unit-test
     * @covers \Cinders\Metadata::startWriting
     */
    public function testStartWriting()
    {
        $this->mock_file
            ->expects($this->atLeastOnce())
            ->method('flock')
            ->will($this->returnValue(true));

        $object = new \Cinders\Metadata($this->mock_file, true);
        $object->startWriting();
    }

   /**
    * @group unit-test
    * @covers \Cinders\Metadata::startWriting
    * @expectedException \RuntimeException
    */
    public function testFailedLock()
    {
        $this->mock_file
            ->expects($this->atLeastOnce())
            ->method('flock')
            ->will($this->returnValue(false));

        $object = new \Cinders\Metadata($this->mock_file, true);
        $object->startWriting();
    }

    /**
     * @group unit-test
     * @covers \Cinders\Metadata::finishWriting
     */
    public function testManuallyFinishWriting()
    {
        $this->mock_file
            ->expects($this->atLeastOnce())
            ->method('flock')
            ->will($this->returnValue(true));

        $object = new \Cinders\Metadata($this->mock_file, true);
        $object->startWriting();
        $object->finishWriting();
    }

   /**
    * @group unit-test
    * @covers \Cinders\Metadata::flushToDisk
    * @expectedException \RuntimeException
    */
    public function testFlushReadonlyFile()
    {
        $object = new \Cinders\Metadata($this->mock_file, true);
        $object->flushToDisk();
    }
}
