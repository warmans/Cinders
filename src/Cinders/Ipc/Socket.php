<?php
namespace Cinders\Ipc;

/**
 * Socket Decorator
 *
 * Wrap a socket resource in an object so we can use it in an OO fashion.
 *
 * @author warmans
 */
class Socket implements \Psr\Log\LoggerAwareInterface
{
    /**
     * The end of transmission char(s). Note these are stripped from data so be careful!
     */
    const EOF = "\0\0";

    /*
     * Number of bytes to send per data block
     */
    const CHUNK_SIZE = 4096;

    private $socket;
    private $socket_config = array();
    private $connected = false;

    private $log;

    /**
     * First param can either be a socket resource or a domain. If a domain is passed a type and protocol
     * may also be required.
     *
     * @param mixed $domain_or_socket
     * @param string $type
     * @param string $protocol
     * @throws \RuntimeException
     */
    public function __construct($domain_or_socket=null, $type=null, $protocol=0)
    {
        if (!is_resource($domain_or_socket)) {
            $this->socket = socket_create($domain_or_socket, $type, $protocol);

            if (!$this->socket) {
                throw $this->getSocketException('Unable to create socket');
            }

            $this->setConf('domain', $domain_or_socket);
            $this->setConf('type', $type);
            $this->setConf('protocol', $protocol);

        } else {
            $this->socket = $domain_or_socket;
        }

        //by default dn't log anywhere
        $this->log = new \Psr\Log\NullLogger();
    }

    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->log = $logger;
    }

    public function getPeerinfo()
    {
        if (!$this->getConf('peer_address')) {

            $addr = $port = null;
            if(!socket_getpeername($this->socket, $addr, $port)){
                return false;
            }

            $this->setConf('peer_address', $addr);
            $this->setConf('peer_port', $port);
        }

        return $this->getConf('peer_address').($this->getConf('peer_port') ? ":{$this->getConf('peer_port')}" : '');
    }

    /**
     * Wrap a socket resource in a new instance of this class
     *
     * @param resource $socket
     * @return \static
     */
    public static function wrapSocket($socket)
    {
        return new static($socket);
    }

    /**
     * Set an option on the underlying socket
     *
     * @param type $level
     * @param type $optname
     * @param type $optval
     */
    public function setOption($level, $optname, $optval)
    {
        socket_set_option($this->socket, $level, $optname, $optval);
    }

    /**
     * Bind to a particular port or socket file.
     *
     * @param type $address
     * @param type $port
     * @throws type
     */
    public function bind($address, $port=null)
    {
        if (!socket_bind($this->socket, $address, $port)){
            throw $this->getSocketException('Bind failed');
        }

        $this->setConf('bind_address', $address);
        $this->setConf('bind_port', $port);
    }

    /**
     * Connect to the given address
     *
     * @param string $address
     * @param mixed $port
     * @throws \RuntimeException
     */
    public function connect($address, $port=null)
    {
        if (!socket_connect($this->socket, $address, $port)) {
            throw $this->getSocketException("Unable to connect to $address".($port ? ":$port" : ''));
        }

        $this->setConnected();

        $this->setConf('connected_address', $address);
        $this->setConf('connected_port', $port);
    }

    /**
     * Listen for incoming connections. Sort of like the oposite of connect.
     *
     * @param int $backlog
     * @throws \RuntimeException
     */
    public function listen($backlog=null)
    {
        if (!socket_listen($this->socket, $backlog)) {
            throw $this->getSocketException("Listen failed");
        }

        $this->setConf('listen_backlog', $backlog);
    }

    /**
     * Accept a socket connection
     *
     * @return Socket
     * @throws \RuntimeException
     */
    public function accept()
    {
        if (!($connected = socket_accept($this->socket))) {
            throw $this->getSocketException("Listen failed");
        }

        //wrap raw socket
        $new_socket = self::wrapSocket($connected);
        $new_socket->setLogger($this->log);
        $new_socket->setConnected();
        return $new_socket;
    }

    /**
     * Send some data over the socket
     *
     * @param string $buffer data to send
     * @return boolean
     * @throws \Exception
     */
    public function write(Package $data)
    {
        //serialise package to string
        $data = $data->serialise();

        $buffer_len = strlen($data);
        $buffer_sent = 0;

        $this->log->debug(">-- Sending...   $buffer_len Bytes | ".$data);

        while ($buffer_sent < $buffer_len) {

            //do send
            $sent = @socket_write(
                $this->socket,
                $this->sanitizeData(substr($data, $buffer_sent)),
                $buffer_len-$buffer_sent
            );

            //check errors
            if ($sent === false) {
                return false;
            }

            //acumulate size
            $buffer_sent += $sent;

            //debug
            $this->log->debug("->- Sending...   ".$buffer_sent." ($buffer_len) Bytes");
        }

        //terminate
        socket_write($this->socket, self::EOF);

        // debug
        $this->log->debug("--> Sending...   Complete $buffer_sent Bytes");

        //sent OK
        return true;
    }

    /**
     * Recieve some data from a socket
     *
     * @param resource $socket
     * @param int $timeout
     * @return Package
     * @throws \RuntimeException
     */
    public function read()
    {
        $bytes_recieved = 0;
        $recieved = $buffer = '';

        $this->log->debug("--< Recieving...");

        while($buffer = socket_read($this->socket, self::CHUNK_SIZE)) {

            //accumulate total bytes recieved
            $bytes_recieved += strlen($buffer);

            //debugging
            $this->log->debug("-<- Recieving... ".strlen($buffer)." ($bytes_recieved) Bytes");

            //check for termination char
            if(strstr($buffer, self::EOF)){
                //add final data to response excluding null bytes
                $recieved .= str_replace(self::EOF, '', $buffer);
                break;
            }

            //aggregate message
            $recieved .= $buffer;
        }

        $this->log->debug("<-- Recieving... Complete $bytes_recieved Bytes | ".$recieved);

        if (!$buffer) {
            $this->log->debug("-/- Connection Closed");
            $this->setConnected(false);
        }

        if ($recieved) {
            return Package::unserialise($recieved);
        }

        return false;
    }

    /**
     * Return undelying socket resource.
     *
     * @return resource
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Check if this is a duplicate of another socket
     *
     * @param \Cinders\JobServer\Transport\Socket\Socket $socket
     */
    public function sameAs(Socket $socket)
    {
        return ($socket->getSocket() == $this->getSocket());
    }

    /**
     * Close the socket
     */
    public function close()
    {
        socket_shutdown($this->socket);
        socket_close($this->socket);
        $this->connected = false;
    }

    /**
     * We store all the config that is applied to the socket incase we need to use it laster
     *
     * @param string $key
     * @param string $val
     */
    protected function setConf($key, $val)
    {
        $this->socket_config[$key] = $val;
    }

    /**
     * Get socket config
     *
     * @param string $key
     * @return string|null
     */
    public function getConf($key)
    {
        return (isset($this->socket_config[$key])) ? $this->socket_config[$key] : null;
    }

    /**
     * Is the socket currently connected to something?
     *
     * @return bool
     */
    public function isConnected()
    {
        return $this->connected;
    }

    public function setConnected($status=true)
    {
        $this->connected = $status;
    }

    /**
     * Create exception including socket error
     *
     * @param string $msg
     * @return \RuntimeException
     */
    protected function getSocketException($msg)
    {
        $errorcode = socket_last_error();
        $msg = $msg.': '.socket_last_error().' ('.socket_strerror($errorcode).')';
        socket_clear_error();
        return new \RuntimeException($msg);
    }

    /**
     * Clear EOL chars from data so we can't prematurely end a transmission.
     *
     * @param string $buffer
     */
    protected function sanitizeData($data)
    {
        return str_replace(self::EOF, '', $data);
    }
}
