<?php
/**
 * @author warmans
 */
namespace Cinders\Project;

class BuildTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \Cinders\Build
     */
    private $object;

    public function setUp()
    {
        $this->cleanUp();

        $this->object = new Build(
            new \Cinders\Metadata(new \SplFileObject(TEST_PROJECTS.'/foo/builds/foobuild/build.meta'), true),
            new \Cinders\Filesystem()
        );
    }

    private function cleanUp()
    {
        $fs = new \Cinders\Filesystem();
        //remove test project
        if ($fs->exists(TEST_PROJECTS.'/bar/builds/testbuild')) {
            $fs->remove(TEST_PROJECTS.'/bar/builds/testbuild');
        }
    }


    /**
     * @group unit-test
     * @covers \Cinders\Project\Build::__construct
     * @covers \Cinders\Project\Build::meta
     */
    public function testGetMeta()
    {
        $this->assertEquals('foobuild', $this->object->meta()->build->name);
    }

    /**
     * @group unit-test
     * @covers \Cinders\Project\Build::getBuildPath
     */
    public function testGetBuildPath()
    {
        $this->assertEquals(TEST_PROJECTS.DS.'foo'.DS.'builds'.DS.'foobuild', $this->object->getBuildPath());
    }

    /**
     * @group unit-test
     * @covers \Cinders\Project\Build::getReportsPath
     */
    public function testGetReportsPath()
    {
        $this->assertEquals(
            TEST_PROJECTS.DS.'foo'.DS.'builds'.DS.'foobuild'.DS.'reports',
            $this->object->getReportsPath()
        );
    }
}
