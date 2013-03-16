<?php
/**
 * @author warmans
 */
namespace Cinders;

class CindersTest extends \PHPUnit_Framework_TestCase {

    private $object;

    public function setUp()
    {
        $this->object = new Cinders(TEST_PROJECTS, new \Cinders\Filesystem());
    }

    /**
     * @group unit-test
     * @covers \Cinders\Cinders::__construct
     * @covers \Cinders\Cinders::getProjects
     */
    public function testGetProjects()
    {
        $projects = $this->object->getProjects();
        $this->assertEquals(1, count($projects));
        $this->assertEquals('foo', $projects[0]->meta()->project->name);
    }
}
