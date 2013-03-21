<?php
/**
 * Package Test
 *
 * @author warmans
 */
namespace Cinders\Ipc;

class PackageTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \Cinders\Ipc\Package;
     */
    private $object;

    public function setUp()
    {
        $this->object = new Package(Package::TYPE_MESSAGE, array('foo'=>'bar'), 123);
    }

    /**
     * @group unit-test
     */
    public function testGetType()
    {
        $this->assertEquals(Package::TYPE_MESSAGE, $this->object->getType());
    }

    /**
     * @group unit-test
     */
    public function testGetPayload()
    {
        $this->assertEquals(array('foo'=>'bar'), $this->object->getPayload());
    }

    /**
     * @group unit-test
     */
    public function testGetIdentifier()
    {
        $this->assertEquals(123, $this->object->getIdentifier());
    }

    /**
     * @group unit-test
     */
    public function testGetDefaultIdentifier()
    {
        $object = new Package(Package::TYPE_MESSAGE, 'foo');
        $this->assertTrue(($object->getIdentifier() > 0));
    }

    /**
     * @group unit-test
     */
    public function testSerialiseUnserialiseArray()
    {
        $serialised = $this->object->serialise();
        $unserialised = Package::unserialise($serialised);

        $this->assertEquals(Package::TYPE_MESSAGE, $unserialised->getType());
        $this->assertEquals(array('foo'=>'bar'), $unserialised->getPayload());
    }

    /**
     * @group unit-test
     */
    public function testSerialiseUnserialiseString()
    {
        $object = new Package(Package::TYPE_MESSAGE, 'foo');
        $serialised = $object->serialise();
        $unserialised = Package::unserialise($serialised);

        $this->assertEquals(Package::TYPE_MESSAGE, $unserialised->getType());
        $this->assertEquals('foo', $unserialised->getPayload());
    }

    /**
     * @group unit-test
     */
    public function testSerialiseUnserialiseObj()
    {
        $pl = new \stdClass();
        $pl->foo = 'bar';

        $object = new Package(Package::TYPE_MESSAGE, $pl);
        $serialised = $object->serialise();
        $unserialised = Package::unserialise($serialised);

        $this->assertEquals(Package::TYPE_MESSAGE, $unserialised->getType());
        $this->assertEquals('bar', $unserialised->getPayload()->foo);
    }
}
