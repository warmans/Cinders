<?php
/**
 * @author warmans
 */
namespace Cinders;

class ProjectTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \Cinders\Project
     */
    private $object;

    public function setUp()
    {
        $this->object = new Project(
            new Metadata(new \SplFileObject(TEST_PROJECTS.'/foo/project.meta'), true),
            new Filesystem()
        );
    }

    /**
     * @covers \Cinders\Project::__construct
     * @covers \Cinders\Project::meta
     */
    public function testGetMeta()
    {
        $this->assertEquals('foo', $this->object->meta()->project->name);
    }

    public function testGetProjectPath()
    {
        $this->assertEquals(TEST_PROJECTS.DS.'foo', $this->object->getProjectPath());
    }

    public function testGetWorkspacePath()
    {
        $this->assertEquals(TEST_PROJECTS.DS.'foo/workspace', $this->object->getWorkspacePath());
    }

    public function testGetBuildsPath()
    {
        $this->assertEquals(TEST_PROJECTS.DS.'foo/builds', $this->object->getBuildsPath());
    }

    /**
     * @covers \Cinders\Project::getBuilds
     */
    public function testGetBuilds()
    {
        $builds = $this->object->getBuilds();
        $this->assertEquals(1, count($builds));
        $this->assertEquals('foobuild', $builds[0]->meta()->build->name);
    }
}
