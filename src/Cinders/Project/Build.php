<?php
namespace Cinders\Project;

/**
 * Build
 *
 * @author Stefan
 */
class Build extends \Cinders\Artifact
{
    /**
     * Create a new build
     *
     * @param type $path
     * @param \Cinders\Filesystem $filesystem
     */
    public static function init($path, \Cinders\Filesystem $filesystem, $name=null)
    {
        $build_name = $name ?: self::createName();
        $build_path = $path.DIRECTORY_SEPARATOR.$build_name;
        $build_meta_path = $build_path.DIRECTORY_SEPARATOR.'build.meta';

        //make a directory
        $filesystem->mkdir($build_path);

        //make metadata file
        $metadata = new \Cinders\Metadata(new \SplFileObject($build_meta_path, 'w+'));
        $metadata->setData(array(
            'build'=>array(
                'name'=>$build_name,
                'created_date'=>date('Y-m-d H:i:s')
            )
        ));
        $metadata->finishWriting();

        //return new build instance
        return new static($metadata, $filesystem);
    }

    private static function createName()
    {
        return date('Y_m_d_H_i_s_').getmygid();
    }

    public function getBuildPath(){
        return dirname($this->metadata->getLocationOnDisk());
    }

    public function getReportsPath()
    {
        return $this->getBuildPath().DIRECTORY_SEPARATOR.'reports';
    }
}