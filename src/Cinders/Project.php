<?php
namespace Cinders;

/**
 * Project
 *
 * @author warmans
 */
class Project extends Artifact
{
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
        foreach ($this->filesystem->findFiles('build.meta', $this->getBuildsPath()) as $build_meta_file) {
            $builds[] = new Project\Build(new Metadata(new \SplFileObject($build_meta_file)), $this->filesystem);
        }

        return $builds;
    }

    public function build()
    {
        $build = \Cinders\Project\Build::init($this->getBuildsPath(), $this->filesystem);

        $this->getBuilder()->build($this, $build);

        //do more build stuff
    }

    private function getBuilder()
    {
        return new \Cinders\Project\Builder\Phing();
    }
}
