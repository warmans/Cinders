<?php
namespace Cinders\Project\Builder\Phing;

/**
 * Phing Builder
 *
 * @author Stefan
 */
class Phing
{
    private $build_file_name;

    public function __construct($build_file_name='phing.xml')
    {
        $this->build_file_name = $build_file_name;
    }

    public function build(\Cinders\Project $project)
    {
        $build_listener = new \BuildListener();

        $build_file_path = $project->getWorkspacePath().DIRECTORY_SEPARATOR.$this->build_file_name;

        $phing = new \Phing();
        $phing->execute(
            array('-buildfile', $build_file_path, '-listener', $build_listener )
        );
        $phing->runBuild();

        //todo determine the success of the build
        return true;
    }
}