<?php
/**
 * @author warmans
 */
namespace Cinders;

class CindersTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Cinders\Cinders
     */
    private $object;

    public function setUp()
    {
        $this->cleanUp();
        $this->object = new Cinders(TEST_PROJECTS, new \Cinders\Filesystem());
    }

    public function tearDown()
    {
        $this->cleanUp();
    }

    private function cleanUp()
    {
        $fs = new \Cinders\Filesystem();
        //remove test project
        if (is_dir(TEST_PROJECTS.'/baz')) {
            $fs->remove(TEST_PROJECTS.'/baz');
        }
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

    /**
     * @group unit-test
     * @covers \Cinders\Cinders::newProject
     */
    public function testNewProject()
    {
        $project = $this->object->newProject('baz');
        $this->assertEquals('baz', $project->meta()->project->name);
    }


    /**
     * @group unit-test
     * @covers \Cinders\Cinders::newProject
     * @expectedException \RuntimeException
     */
    public function testNewWithDuplicateName()
    {
        $this->object->newProject('foo');
    }
}
