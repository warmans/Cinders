<?php
namespace Cinders\Report;
/**
 * Report Data
 *
 * @author Stefan
 */
class Data
{
    private $data = array();

    public function setData($name, $data){
        $this->data[$name] = $data;
    }

    public function exportString()
    {
        return json_encode($this->data);
    }
}