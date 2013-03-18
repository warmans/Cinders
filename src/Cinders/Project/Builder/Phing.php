<?php
namespace Cinders\Project\Builder;

/**
 * Phing Builder
 *
 * @author Stefan
 */
class Phing extends \Cinders\Project\Builder
{
    private $build_file_name;

    public function __construct($build_file_name='phing.xml')
    {
        $this->build_file_name = $build_file_name;
    }

    public function build(\Cinders\Project $project, \Cinders\Project\Build $build)
    {
        $timer = \Cinders\Util\Timer::start();

        $build_listener = new Phing\BuildListener();
        $build_file_path = $project->getWorkspacePath().DIRECTORY_SEPARATOR.$this->build_file_name;

        //run the build
        $phing = new \Phing();
        $phing->execute(
            array('-buildfile', $build_file_path, '-listener', $build_listener )
        );
        $phing->runBuild();

        //@todo determine the success of the build
        $success = true;

        //add meta to the build
        $build->meta()->startWriting();
        $build->meta()->addData(
            array(
                'build'=>array(
                    'success'=>$success,
                    'time_taken'=>$timer->elapsed()
                )
            )
        );
        $build->meta()->finishWriting();

        return $build;
    }
}
