<?php
namespace Cinders\Project;

/**
 * Build
 *
 * @author Stefan
 */
class Build extends \Cinders\Artifact
{
    const META_NAME = 'build.meta';

    public function getBuildPath(){
        return dirname($this->metadata->getLocationOnDisk());
    }

    public function getReportsPath()
    {
        return $this->getBuildPath().DIRECTORY_SEPARATOR.'reports';
    }

    public function getBuildOutputPath()
    {
        return $this->getBuildPath().DIRECTORY_SEPARATOR.'output';
    }

    public function init()
    {
        $this->filesystem->mkdir($this->getReportsPath());
        $this->filesystem->mkdir($this->getBuildOutputPath());
    }

    public function destroy()
    {
        $this->meta()->finishWriting();
        $this->filesystem->remove($this->getBuildPath());
    }
}
