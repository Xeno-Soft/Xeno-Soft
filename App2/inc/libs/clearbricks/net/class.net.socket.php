<?php
/**
 * @class netSocket
 * @brief Network base
 *
 * This class handles network socket through an iterator.
 *
 * @package Clearbricks
 * @subpackage Network
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class netSocket
{
    protected $_host;           ///< string: Server host
    protected $_port;           ///< integer: Server port
    protected $_transport = ''; ///< string: Server transport
    protected $_timeout;        ///< integer: Connection timeout
    protected $_handle;         ///< resource: Resource handler

    /**
     * Class constructor
     *
     * @param string        $host        Server host
     * @param string         $port        Server port
     * @param int        $timeout        Connection timeout
     */
    public function __construct($host, $port, $timeout = 10)
    {
        $this->_host    = $host;
        $this->_port    = abs((int) $port);
        $this->_timeout = abs((int) $timeout);
    }

    /**
     * Object destructor
     *
     * Calls {@link close()} method
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Get / Set host
     *
     * If <var>$host</var> is set, set {@link $_host} and returns true.
     * Otherwise, returns {@link $_host} value.
     *
     * @param string    $host            Server host
     * @return string|true
     */
    public function host($host = null)
    {
        if ($host) {
            $this->_host = $host;

            return true;
        }

        return $this->_host;
    }

    /**
     * Get / Set port
     *
     * If <var>$port</var> is set, set {@link $_port} and returns true.
     * Otherwise, returns {@link $_port} value.
     *
     * @param integer    $port            Server port
     * @return integer|true
     */
    public function port($port = null)
    {
        if ($port) {
            $this->_port = abs((int) $port);

            return true;
        }

        return $this->_port;
    }

    /**
     * Get / Set timeout
     *
     * If <var>$timeout</var> is set, set {@link $_timeout} and returns true.
     * Otherwise, returns {@link $_timeout} value.
     *
     * @param integer    $timeout            Connection timeout
     * @return string|true
     */
    public function timeout($timeout = null)
    {
        if ($timeout) {
            $this->_timeout = abs((int) $timeout);

            return true;
        }

        return $this->_timeout;
    }

    /**
     * Set blocking
     *
     * Sets blocking or non-blocking mode on the socket.
     *
     * @param integer    $i        1 for yes, 0 for no
     * @return    boolean
     */
    public function setBlocking($i)
    {
        if (!$this->isOpen()) {
            return false;
        }

        return stream_set_blocking($this->_handle, (bool) $i);
    }

    /**
     * Open connection.
     *
     * Opens socket connection and Returns an object of type {@link netSocketIterator}
     * which can be iterate with a simple foreach loop.
     *
     * @return    netSocketIterator|bool
     */
    public function open()
    {
        $handle = @fsockopen($this->_transport . $this->_host, $this->_port, $errno, $errstr, $this->_timeout);
        if (!$handle) {
            throw new Exception('Socket error: ' . $errstr . ' (' . $errno . ')');
        }
        $this->_handle = $handle;

        return $this->iterator();
    }

    /**
     * Closes socket connection
     */
    public function close()
    {
        if ($this->isOpen()) {
            fclose($this->_handle);
            $this->_handle = null;
        }
    }

    /**
     * Send data
     *
     * Sends data to current socket and returns an object of type
     * {@link netSocketIterator} which can be iterate with a simple foreach loop.
     *
     * <var>$data</var> can be a string or an array of lines.
     *
     * Example:
     *
     * <code>
     * <?php
     * $s = new netSocket('www.google.com',80,2);
     * $s->open();
     * $data = [
     *     'GET / HTTP/1.0'
     * ];
     * foreach($s->write($data) as $v) {
     *     echo $v."\n";
     * }
     * $s->close();
     * ?>
     * </code>
     *
     * @param string|array    $data        Data to send
     * @return    netSocketIterator|false
     */
    public function write($data)
    {
        if (!$this->isOpen()) {
            return false;
        }

        if (is_array($data)) {
            $data = implode("\r\n", $data) . "\r\n\r\n";
        }

        fwrite($this->_handle, $data);

        return $this->iterator();
    }

    /**
     * Flush buffer
     *
     * Flushes socket write buffer.
     */
    public function flush()
    {
        if (!$this->isOpen()) {
            return false;
        }

        fflush($this->_handle);
    }

    /**
     * Iterator
     *
     * @return    netSocketIterator|false
     */
    protected function iterator()
    {
        if (!$this->isOpen()) {
            return false;
        }

        return new netSocketIterator($this->_handle);
    }

    /**
     * Is open
     *
     * Returns true if socket connection is open.
     *
     * @return    boolean
     */
    public function isOpen()
    {
        return is_resource($this->_handle);
    }
}

/**
 * @class netSocketIterator
 * @brief Network socket iterator
 *
 * This class offers an iterator for network operations made with
 * {@link netSocket}.
 *
 * @see netSocket::write()
 */
class netSocketIterator implements Iterator
{
    protected $_handle; ///< resource: Socket resource handler
    protected $_index;  ///< integer: Current index position

    /**
     * Constructor
     *
     * @param resource    $handle        Socket resource handler
     */
    public function __construct(&$handle)
    {
        if (!is_resource($handle)) {
            throw new Exception('Handle is not a resource');
        }
        $this->_handle = &$handle;
        $this->_index  = 0;
    }

    /* Iterator methods
    --------------------------------------------------- */
    /**
     * Rewind
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        # Nothing
    }

    /**
     * Valid
     *
     * @return boolean    True if EOF of handler
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return !feof($this->_handle);
    }

    /**
     * Move index forward
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->_index++;
    }

    /**
     * Current index
     *
     * @return integer    Current index
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->_index;
    }

    /**
     * Current value
     *
     * @return string    Current socket response line
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return fgets($this->_handle, 4096);
    }
}
