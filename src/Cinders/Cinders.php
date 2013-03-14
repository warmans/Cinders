<?php
namespace Cinders;

/**
 * Cinders
 *
 * @author Stefan
 */
class Cinders
{
    private $projects_root;

    private $filesystem;

    public function __construct($projects_root, Filesystem $filesystem)
    {
        $this->projects_root = $projects_root;
        $this->filesystem = $filesystem;
    }

    public function getProjects()
    {
        $projects = array();
        foreach($this->filesystem->findFiles('project.meta', $this->projects_root) as $file){
            $projects[] = new Project(new Metadata(new \SplFileObject($file)), $this->filesystem);
        }
        return $projects;
    }
}
