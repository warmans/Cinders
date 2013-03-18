<?php
/**
 * @author warmans
 */
namespace Cinders;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \Cinders\Project
     */
    private $object;

    public function setUp()
    {
        $this->cleanUp();

        $this->object = new Project(
            new Metadata(new \SplFileObject(TEST_PROJECTS.'/foo/project.meta'), true),
            new Filesystem()
        );
    }

    private function cleanUp()
    {
        $fs = new \Cinders\Filesystem();
        //remove test project
        if ($fs->exists(TEST_PROJECTS.'/foo/builds/testbuild')) {
            $fs->remove(TEST_PROJECTS.'/foo/builds/testbuild');
        }
    }

    /**
     * @group unit-test
     * @covers \Cinders\Project::__construct
     * @covers \Cinders\Project::meta
     */
    public function testGetMeta()
    {
        $this->assertEquals('foo', $this->object->meta()->project->name);
    }

    /**
     * @group unit-test
     */
    public function testGetProjectPath()
    {
        $this->assertEquals(TEST_PROJECTS.DS.'foo', $this->object->getProjectPath());
    }

    /**
     * @group unit-test
     */
    public function testGetWorkspacePath()
    {
        $this->assertEquals(TEST_PROJECTS.DS.'foo/workspace', $this->object->getWorkspacePath());
    }

    /**
     * @group unit-test
     */
    public function testGetBuildsPath()
    {
        $this->assertEquals(TEST_PROJECTS.DS.'foo/builds', $this->object->getBuildsPath());
    }

    /**
     * @group unit-test
     * @covers \Cinders\Project::getBuilds
     */
    public function testGetBuilds()
    {
        $builds = $this->object->getBuilds();
        $this->assertEquals(1, count($builds));
        $this->assertEquals('foobuild', $builds[0]->meta()->build->name);
    }

    /**
     * @group unit-test
     * @covers \Cinders\Project::build
     */
    public function testBuild()
    {
        $build = $this->object->build('testbuild');
        $this->assertTrue($build instanceof \Cinders\Project\Build);
        $this->assertTrue(file_exists($build->getBuildOutputPath().'/phing.log'));
    }
}
