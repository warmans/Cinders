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

        $build_listener = '\\Cinders\\Project\\Builder\\Phing\\BuildListener';
        $build_file_path = $project->getWorkspacePath().DIRECTORY_SEPARATOR.$this->build_file_name;

        try {
            //run the build
            $phing = new \Phing();
            $phing->startup();

            //add some properties
            \Phing::setProperty('cinders.build_output', $build->getBuildOutputPath());

            $phing->execute(
                array(
                    '-buildfile',
                    $build_file_path,
                    '-listener',
                    $build_listener,
                    '-logfile',
                    $build->getBuildOutputPath().'/phing.log'
                )
            );
            $phing->runBuild();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
        }

        //add meta to the build

        $build->meta()->startWriting();
        $build->meta()->setData(
            array(
                'build'=>array(
                    'success'=>$success,
                    'time_taken'=>$timer->elapsed(),
                )
            )
        );
        $build->meta()->finishWriting();

        return $build;
    }
}
