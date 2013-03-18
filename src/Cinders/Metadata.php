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
        //underlying file for metadata
        $this->file = $file;

        if (!$read_only) {
            $this->startWriting();
        }
        $this->read_only = $read_only;

        //init the data member
        $this->reloadFromDisk();
    }

    public function __destruct()
    {
        $this->finishWriting();
    }

    public function __get($name)
    {
        return $this->data->{$name};
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
            $this->data = $this->data ?: new \stdClass();
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

        //reset file
        $this->file->ftruncate(0);
        $this->file->rewind();

        if ($this->file->fwrite($this->encodeData($this->data)) !== null) {
            //ensure consistent data between fresh data and data pulled off disk (i.e. data is always an object)
            $this->reloadFromDisk();
            return true;
        }

        throw new \RuntimeException('Flush to disk failed');
    }

    private function encodeData($data)
    {
        return json_encode($data);
    }

    /**
     * Merge some new data into the metadta
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $this->mergeData($this->data, $data);
        $this->flushToDisk();
    }

    private function mergeData(\stdClass $old_data, array $new_data)
    {
        foreach ($new_data as $key => $val) {

            //existing value for key
            if (isset($old_data->$key)) {

                if (is_object($old_data->$key)) {
                    if (is_array($val)) {
                        //old and new values are both complex - recurse
                        $old_data->$key = $this->mergeData($old_data->$key, $val);
                    } else {
                        //new val is not an array - replace object with value
                        $old_data->$key = $val;
                    }
                } else {
                    //old value is just a single value - replacement
                    $old_data->$key = json_decode(json_encode($val));
                }
            } else {
                //no old key - addition
                $old_data->$key = json_decode(json_encode($val));
            }
        }
        return $old_data;
    }

    public function getLocationOnDisk()
    {
        return $this->file->getRealPath();
    }
}
