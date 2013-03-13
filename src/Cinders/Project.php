<?php
namespace Cinders;

/**
 * Project
 *
 * @author warmans
 */
class Project
{
    /**
     *
     * @var \Cinders\Metadata
     */
    private $metadata;

    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

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
}