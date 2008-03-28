<?php // VERIFIED

    /**
     * IRC Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * IRC Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class irc
    {
         /**
         * IRC server-object
         * @access public
         * @var string
         */
        var $server;

        /**
         * IRC channels to autojoin, separated by space
         * @access public
         * @var string
         */
        var $channels_to_autojoin;
        
        /**
         * Channels Object
         * @access public
         * @var object
         *
         */
        var $channels;

        /**
         * @access private
         * @var object
         */
        var $socket;

        /**
         * @access private
         * @var float
         */
        var $max_send_interval = 1.4;

        /**
         * @access private
         * @var float
         */
        var $max_receive_interval = 1.0;

        /**
         * @access private
         * @var int timestamp
         */
        var $last_send = 0;

        /**
         * @access private
         * @var int timestamp
         */
        var $last_received = 0;

        /**
         * @access private
         * @var array
         */
        var $sendQ;

        /**
         * Set to true to quit
         * @access public
         * @var boolean
         */
        var $quit = false;

        /**
         * @access private
         */
        var $sendId = 0;

        /**
         * @access private
         */
        var $last_ping = 0;

        /**
         * Attempts to connect to IRC server. Returns array(true,'logstring) on success
         * @return array
         */
        function connectToServer()
        {
            if(!$this->server->bot_nick_name || !$this->server->bot_alt_nick_name || !$this->server->host || !$this->server->port) {
                return array(false, 'Missing critical setting(s). '.__FILE__.' at '.__LINE__);
            }

            unset($this->sendQ);
            $this->sendQ = array();

            $this->socket = new socket($this->server->host, $this->server->port);
            if(!$this->socket->connect()) {
                return array(false, $this->socket->error);
            }
            $max_send_interval_store    = $this->max_send_interval;
            $max_receive_interval_store =  $this->max_receive_interval;

            $this->max_send_interval   = 0;
            $this->max_receive_interval = 0;

            $this->sendBotInfo();

            while(!$this->server->connected) {
                $this->doSend();
                $this->doReceive();
                usleep(300000);
            }

            foreach($this->channels_to_autojoin as $channel) {
                $this->send(CMD_JOIN . $channel);
            }

            $this->max_send_interval   = $max_send_interval_store;
            $this->max_receive_interval = $max_receive_interval_store;
            $this->server->connected = true;
            return array(true, 'Bot connected to ' . $this->server->host . ':' . $this->server->port);
        }
        /**
         * sends connection info
         * @param boolean $useAlternativeNickname
         */
        function sendBotInfo($use_alternative_nickname = false)
        {
            if($use_alternative_nickname) {
                $this->send(CMD_NICK . $this->server->bot_alt_nick_name);
            } else {
                $this->send(CMD_NICK . $this->server->bot_nick_name);
                $this->send(CMD_USER . $this->server->bot_user_name . ' ' . $this->server->host . ' bot :' . $this->server->bot_user_name);
            }
        }

        /**
         * Put data in send queue
         * @param string $data the string to send
         * @param int    $time when to send this data, as unix timestamp
         */
        function send($data, $time = 0)
        {
            if($time == 0) {
                $this->sendId++;
                $time = $this->sendId;
            }
            $data .= "\n";
            $this->sendQ[] = array($time, $data);
            asort($this->sendQ);
            $this->sendQ = array_values($this->sendQ);
        }

        /**
         * Handle incoming data
         * @access private
         * @param string $data
         */
        function _recieve($data)
        {
            $buffer = new buffer($data, RECEIVE, $this->server, $this->channels);

            if(!$this->_pong($buffer->all) && strlen($buffer->all) > 0) {
                if(trim($data)) {
                    $this->logger->addBotLog('<- '.trim($data),BB_LOG_NONE);
                }
                $returnString = $this->moduleHandler->check($buffer);
                $this->logger->channels($buffer);
                if($returnString) {
                    $this->logger->addBotLog('## ' . $returnString, BB_LOG_MODULE_CALL, COLOR_GREEN);
                }
                if(!$this->server->connected) {
                    if($buffer->command == '001') {
                        $this->server->connected = true;
                        $this->server->connect_time = time();
                    } elseif($buffer->command == '433') {
                         $this->logger->addBotLog('## Nickname ' . $this->server->bot_nick_name . ' is taken, trying "' . $this->server->bot_alt_nick_name . '" instead.', BB_LOG_NOTICE, COLOR_YELLOW);
                         sleep(1);
                         $this->sendBotInfo(true);
                    } elseif($buffer->command == '376') {

                    }
                 }
             }
             $buffer = null;
         }
        /**
         * Send data from queue
         */
        function doSend()
        {
            $sendDiff = helpers::microtimeFloat() - $this->max_send_interval;
            $this->_ping();
            if($sendDiff > $this->last_send && count($this->sendQ) > 0 && $sendDiff > $this->sendQ[0][0]) {
                list($time, $data) = array_shift($this->sendQ);
                if(!$this->socket->send($data)) {
                    $this->server->connected = false;
                }
                $data = trim($data);
                $this->last_send = helpers::microtimeFloat();
                $this->logger->addBotLog('-> '.$data, BB_LOG_NONE);

                if($this->server->user_host) {
                    $data = ':' . $this->server->user_host . ' ' .  $data;
                }

                $buffer =  new buffer($data, SEND, $this->server,$this->channels);
                $this->logger->channels($buffer);
                $buffer = null;
            } elseif(count($this->sendQ) == 0) {
                $this->sendId = 0;
            }
        }

        /**
         * Receive data from socket
         */
        function doReceive()
        {
            $reciveDiff = helpers::microtimeFloat() - $this->max_receive_interval;
            if($reciveDiff > $this->last_received) {
                $input = $this->socket->receive();
                if($input === false) {
                    $this->server->connected = false;
                } elseif($input) {
                    $this->last_ping = time();
                    $this->last_received = helpers::microtimeFloat();
                    $input = explode("\n", $input);
                    foreach($input as $data) {
                        if(substr(trim($data), 0, 5) == 'ERROR') {
                            $this->server->connected = false;
                        } elseif(strlen($data) > 0) {
                            $this->_recieve($data);
                        }
                    }
                }
            }
        }

        /**
         * Send ping and listen or response
         * @access private
         */
        function _ping()
        {
            $time = time();
            if($this->last_ping <  $time - 300) {
                $this->socket->send('PING bambotoo' . "\n");
            }
            if($this->last_ping < $time - 500) {
                $this->server->connected = false;
            }
        }

        /**
         * Send pong on server ping
         * @access private
         * @return boolean true if successful
         */
        function _pong($raw)
        {
            if(substr($raw, 0, 6) == 'PING :') {
                $data = 'PONG :' . substr($raw, 6) . "\n";
                $this->socket->send($data);
                $this->server->connect_time = time();
                return true;
            } else {
                return false;
            }

        }

        /**
         * Send a CTCP Version reply
         * @param string $sendToNick nick to send it to
         */
        function sendVersionReply($sendToNick)
        {
            $this->send(CMD_NOTICE . $sendToNick . ' :' .chr(001) . 'VERSION bambotoo-' . APP_VERSION .' - http://www.lejban.se/bambotoo' .chr(001));
        }
    }

?>