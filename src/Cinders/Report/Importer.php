<?php
namespace Cinders\Report;

/**
 * Importer
 *
 * Import a raw report (e.g. phpcs, phploc etc.) into a standard format.
 *
 * @author Stefan
 */
abstract class Importer
{
    private $import_path;
    private $filesystem;

    /**
     * @param string $import_path file or directory where import lives. The concrete importer is expected to know which.
     * @param \Cinders\Filesystem $filesystem
     */
    public function __construct($import_path, \Cinders\Filesystem $filesystem)
    {
        $this->import_path = $import_path;
        $this->filesystem = $filesystem;
    }

    /**
     * Import data and return a Report\Data object
     *
     * @return \Cinders\Report\Data
     */
    abstract public function import();
}
