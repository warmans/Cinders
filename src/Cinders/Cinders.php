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

    public function newProject($name)
    {
        $project_path = $this->projects_root.DIRECTORY_SEPARATOR.$name;
        $project_meta_path = $project_path.DIRECTORY_SEPARATOR.Project::META_NAME;

        //check we're not about to ruin an existing project
        if ($this->filesystem->exists($project_path)) {
            throw new \RuntimeException("Project called $name already exists");
        }

        //make a directory
        $this->filesystem->mkdir($project_path);

        //make metadata file
        $metadata = new \Cinders\Metadata(new \SplFileObject($project_meta_path, 'w+'));
        $metadata->setData(array(
            'project'=>array(
                'name'=>$name,
                'created_date'=>date('Y-m-d H:i:s')
            )
        ));
        $metadata->finishWriting();

        //return new build instance
        return new Project($metadata, $this->filesystem);
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
