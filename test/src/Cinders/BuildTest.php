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
        //testbuild
        if(is_file(TEST_PROJECTS.'/bar/builds/testbuild')){
            rmdir(TEST_PROJECTS.'/bar/builds/testbuild');
        }

        $this->object = new Build(
            new \Cinders\Metadata(new \SplFileObject(TEST_PROJECTS.'/foo/builds/foobuild/build.meta'), true),
            new \Cinders\Filesystem()
        );
    }

    /**
     * @covers \Cinders\Project\Build::__construct
     * @covers \Cinders\Project\Build::meta
     */
    public function testGetMeta()
    {
        $this->assertEquals('foobuild', $this->object->meta()->build->name);
    }

    /**
     * @covers \Cinders\Project\Build::getBuildPath
     */
    public function testGetBuildPath()
    {
        $this->assertEquals(TEST_PROJECTS.DS.'foo'.DS.'builds'.DS.'foobuild', $this->object->getBuildPath());
    }

    /**
     * @covers \Cinders\Project\Build::getReportsPath
     */
    public function testGetReportsPath()
    {
        $this->assertEquals(
            TEST_PROJECTS.DS.'foo'.DS.'builds'.DS.'foobuild'.DS.'reports',
            $this->object->getReportsPath()
        );
    }

    public function testBuildInit()
    {
        $build = Build::init(TEST_PROJECTS.DS.'bar'.DS.'builds', new \Cinders\Filesystem(), 'testbuild');
        $this->assertTrue($build instanceof Build);
        $this->assertEquals('testbuild', $build->meta()->build->name);
    }
}
