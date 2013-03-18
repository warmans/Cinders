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

    public function destroy()
    {
        $this->meta()->finishWriting();
        $this->filesystem->remove($this->getBuildPath());
    }
}