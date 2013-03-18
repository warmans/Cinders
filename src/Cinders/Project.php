<?php
namespace Cinders;

/**
 * Project
 *
 * @author warmans
 */
class Project extends Artifact
{
    const META_NAME = 'project.meta';

    /**
     * Root path of the project
     * @return string
     */
    public function getProjectPath()
    {
        return dirname($this->metadata->getLocationOnDisk());
    }

    /**
     * The workspace is where projects are built
     * @return string
     */
    public function getWorkspacePath()
    {
        return $this->getProjectPath().'/workspace';
    }

    /**
     * The builds dir contains all the builds
     *
     * @return string
     */
    public function getBuildsPath()
    {
        return $this->getProjectPath().'/builds';
    }

    /**
     * Get all builds for this project
     */
    public function getBuilds()
    {
        $builds = array();
        foreach ($this->filesystem->findFiles(Project\Build::META_NAME, $this->getBuildsPath()) as $build_meta_file) {
            $builds[] = new Project\Build(new Metadata(new \SplFileObject($build_meta_file)), $this->filesystem);
        }

        return $builds;
    }

    public function build($name=null)
    {
        $build_name = $name ?: date('Y_m_d_H_i_s_').getmygid();
        $build_path = $this->getBuildsPath().DIRECTORY_SEPARATOR.$build_name;
        $build_meta_path = $build_path.DIRECTORY_SEPARATOR.Project\Build::META_NAME;

        //make a directory
        $this->filesystem->mkdir($build_path);

        //make metadata file
        $metadata = new \Cinders\Metadata(new \SplFileObject($build_meta_path, 'w+'));
        $metadata->setData(array(
            'build'=>array(
                'name'=>$build_name,
                'created_date'=>date('Y-m-d H:i:s')
            )
        ));
        $metadata->finishWriting();

        //get a new build instance
        $build = new Project\Build($metadata, $this->filesystem);
        $build->init();
        
        //build the workspace
        $this->getBuilder()->build($this, $build);

        //@todo do the rest of the build stuff

        return $build;
    }

    private function getBuilder()
    {
        return new \Cinders\Project\Builder\Phing();
    }
}
