<?php
namespace Cinders;

/**
 * Generic metadata e.g. build/project metadata
 *
 * @author warmans
 */
class Metadata
{
    /**
     * @var \SplFileObject
     */
    private $file;

    /**
     * @var object
     */
    private $data;

    /**
     * @var bool
     */
    private $read_only;

    public function __construct(\SplFileObject $file, $read_only=false)
    {
        $this->file = $file;

        if (!$read_only) {
            $this->startWriting();
        }

        $this->read_only = $read_only;

        $this->reloadFromDisk();
    }

    public function __destruct()
    {
        $this->finishWriting();
    }

    public function startWriting()
    {
        if (!$this->file->flock(LOCK_EX)) {
            throw new \RuntimeException('Unable to lock metadata for writing');
        }
    }

    public function finishWriting()
    {
        $this->file->flock(LOCK_UN);
    }

    public function reloadFromDisk()
    {
        $this->file->rewind();

        $serialised = '';
        while($this->file->valid()) {
            $serialised .= $this->file->current() . PHP_EOL;
        }

        //file is empty - that's fine
        if (trim($serialised) == '') {
            $this->data = new \stdClass(); //make it a blank object
            return true;
        }

        if ($this->data = $this->decodeData($serialised)) {
            return true;
        } else {
            throw new \RuntimeException('Unable to decode metadata in file');
        }
    }

    private function decodeData($data)
    {
        return json_decode($data);
    }

    public function flushToDisk()
    {
        if ($this->read_only) {
            throw new \RuntimeException('Data is read only');
        }

        $this->file->ftruncate(0);
        if ($this->file->fwrite($this->encodeData($this->data)) !== null) {
            return true;
        }
        return false;
    }

    private function encodeData($data)
    {
        return json_encode($data);
    }

    public function __get($name)
    {
        return $this->data->{$name};
    }

    public function setData(array $data)
    {
        $this->data = $data;

        if (!$this->flushToDisk()) {
            throw new \RuntimeException('Flush to disk failed');
        }

        //ensure consistent data between fresh data and data pulled off disk (i.e. data is always an object)
        $this->reloadFromDisk();
    }

    public function getLocationOnDisk()
    {
        return $this->file->getRealPath();
    }
}
