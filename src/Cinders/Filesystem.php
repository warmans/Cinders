<?php
namespace Cinders;

/**
 * Filesystem
 *
 * @author warmans
 */
class Filesystem extends \Symfony\Component\Filesystem\Filesystem
{
    /**
     * @param string $path
     * @param string $open_mode
     * @return \SplFileObject
     */
    public function getFile($path, $open_mode='r')
    {
        return new \SplFileObject($path, $open_mode);
    }
}