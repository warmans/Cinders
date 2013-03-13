<?php
namespace Cinders\Api;

/**
 * ActiveRoute
 *
 * @author Stefan
 */
class ActiveRoute
{
    private $raw_path;
    private $segments;
    private $active_segment;

    public function __construct($path)
    {
        $this->raw_path = $path;
        $this->segments = array_values(array_filter(explode('/', $path)));
        $this->active_segment = 0;
    }

    private function getNextKey()
    {
        return array_key_exists(($this->active_segment + 1), $this->segments) ? $this->active_segment + 1 : null;
    }

    public function peekNextSegment()
    {
        return ($this->getNextKey() !== null) ? $this->segments[$this->getNextKey()] : null;
    }

    public function getNextSegment()
    {
        if ($this->getNextKey() === null) {
            return null;
        }

        $this->active_segment++;
        return $this->segments[$this->active_segment];
    }

    public function moveToNextSegment()
    {
        $this->getNextSegment();
        return $this;
    }

    public function skipSegment()
    {
        $this->active_segment++;
        return $this;
    }

    public function getCurrentSegment()
    {
        return array_key_exists($this->active_segment, $this->segments) ? $this->segments[$this->active_segment] : null;
    }
}
