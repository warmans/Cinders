<?php
namespace Cinders;

/**
 * Artifact
 *
 * @author Stefan
 */
abstract class Artifact
{
    /**
     * @var \Cinders\Metadata
     */
    protected $metadata;

    /**
     * @var \Cinders\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Cinders\Metadata $metadata
     * @param \Cinders\Filesystem $filesystem
     */
    public function __construct(Metadata $metadata, Filesystem $filesystem)
    {
        $this->metadata = $metadata;
        $this->filesystem = $filesystem;
    }

    /**
     * Get the metadata object
     *
     * @return object
     */
    public function meta()
    {
        return $this->metadata;
    }
}