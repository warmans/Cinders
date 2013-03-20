<?php
namespace Cinders\Ipc\Socket;

/**
 * Socket Collection
 *
 * @author warmans
 */
class Collection extends \SplObjectStorage
{
    public function select($block_seconds=0, $block_ms=0)
    {
        $sock_to_read = $this->getClientSockets();
        $sock_to_write = array(); //no idea what this is useful for
        $sock_exceptions = array(); //or this...

        if ($changed = socket_select($sock_to_read, $sock_to_write, $sock_exceptions, $block_seconds, $block_ms)) {

            $read_collection = new static();
            foreach ($sock_to_read as $readable_socket) {
                $read_collection->attach(\Cinders\Ipc\Socket::wrapSocket($readable_socket));
            }

            $write_collection = new static();
            foreach ($sock_to_write as $writable_socket) {
                $write_collection->attach(Sock::wrapSocket($writable_socket));
            }

            $exception_collection = new static();
            foreach ($sock_exceptions as $exception_socket) {
                $exception_collection->attach(Sock::wrapSocket($exception_socket));
            }

            return array(
                'changed'=>$changed,
                'read'=>$read_collection,
                'write'=>$write_collection,
                'exception'=>$exception_collection
            );

        }
    }

    public function detach($object)
    {
        parent::detach($object);

        //remove identical objects and objects that have an identical socket
        $this->removeClientBySocket($object->getSocket());
    }

    public function removeClientBySocket($socket)
    {
        if ($object = $this->getClientBySocket($socket)) {
            parent::detach($object);
        }
    }

    public function getClientBySocket($socket)
    {
        foreach($this as $object){
            if($object->getSocket() == $socket){
                return $object;
            }
        }
        return false;
    }

    public function getClientSockets()
    {
        $resources = array();
        foreach($this as $object){
            $resources[] = $object->getSocket();
        }
        return $resources;
    }
}
