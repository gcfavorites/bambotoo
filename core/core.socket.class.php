<?php // VERIFIED

    /**
     * Socket
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Socket Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class socket
    {
        /**
         * @access private
         */
        var $sock;
        /**
         * contains last error
         * @access public
         * @var string
         */
        var $error;

        /**
         * Create a socket
         *
         * @param  string  $server The ip or hostname of the server to connect to
         * @param  int     $port   the port to connect to
         * @return boolean
         */
        function socket($server, $port)
        {
            $this->server = $server;
            $this->port   = $port;
        }
        /**
         * Connect!
         * @return boolean
         */
        function connect()
        {
            $this->sock = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

            if($this->sock < 0) {
                $this->error = 'Could not create socket.';
                return false;
            }
            $result = @socket_connect($this->sock, $this->server, $this->port);
            if (!$result) {
                $this->error = 'Could not connect to: '.$this->server.' on port '.$this->port . '.';
                return false;
            } else {
                return true;
            }

        }
        /**
         * Disconnect!
         * @return boolean
         */
        function disconnect()
        {
            socket_shutdown($this->sock, 2);
            socket_close($this->sock);
            $this->server->connected = false;

        }
        /**
         * Send data to the server
         *
         * @param string $data the string to send
         * @return boolean
         */
        function send($data)
        {
            $result = @socket_write($this->sock, $data, strlen($data));
            if($result === false) {
                $this->server->connected = false;
                $this->error = 'Could not send.';
                return false;
            } else {
                return true;
            }
        }
        /**
         * Recive data from the server
         *
         * @return mixed The data received, an empty string if there no data available or false if there if no connection.
         */
        function receive()
        {
            $sock = array($this->sock);
            if(@socket_select($sock, $write = NULL, $except = NULL, 0) > 0) {
                $input = @socket_read($this->sock, 16384);
                if($input === false) {
                    $this->server->connected = false;
                    $this->error = 'Could not receive.';
                    return false;
                } else {
                    return trim($input);
                }
            } else {
                return '';
            }
        }
    }
?>