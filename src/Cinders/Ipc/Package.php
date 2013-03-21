<?php
namespace Cinders\Ipc;

/**
 * Container for data transmissed between processes.
 *
 * @author warmans
 */
class Package
{
    const TYPE_ACK = 'ack';
    const TYPE_MESSAGE = 'message';

    private $identifier;
    private $type;
    private $payload;

    public function __construct($type, $payload, $identifier=null)
    {
        $this->identifier = $identifier ?: getmypid();
        $this->type = $type;
        $this->payload = $payload;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function serialise()
    {
        return json_encode(
            array('type'=>$this->type, 'payload'=>serialize($this->payload), 'identifier'=>$this->identifier)
        );
    }

    public static function unserialise($serialised)
    {
        $raw = json_decode($serialised, true);
        return new static($raw['type'], unserialize($raw['payload']), $raw['identifier']);
    }
}
