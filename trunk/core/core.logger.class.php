<?php // VERIFIED
    /**
     * Logging
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Logging Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     */
    class logger
    {
        /** The array of channels to log
         * @access private
         * @var array
         */
        var $_log_channels;

        /**
         * An array with numbers corresponding different loglevels
         * these are defined as constants in core.constants.php
         * @access private
         * @var array
         */
        var $loglevels = array();

        /**
         * Refrences to parent object
         * @access private
         * @var object
         */
        var $_bot;

        /**
         * Format when outputting to console
         * @access private
         * @var string
         */
        var $console_date_format;

        /**
         * Format when outputting to a file
         * @access private
         * @var string
         */
        var $log_date_time_format;

        /**
         * @access private
         * @var array
         */
        var $_log_level_description = array();

        /**
         * @access private
         * @var boolean
         */
        var $_output = true;
        
        /**
         * @access private
         * @var boolean
         */
        var $_colored_output = false;
        
        /**
         * Constructor
         * @param object $bot
         */
        function logger(&$bot)
        {
            $this->_bot                 = &$bot;
            $this->server               = &$bot->server;
            $this->_log_channels        = &$bot->_log_channels;
            $this->loglevels            = helpers::toArray($bot->config['log_level']);
            $this->console_date_format  = &$bot->config['console_date_format'];
            $this->log_date_time_format = &$bot->config['log_date_time_format'];
            $this->_colored_output      = &$bot->config['console_colors'];

            $this->_log_level_description[BB_LOG_NONE]          = 'Log none';
            $this->_log_level_description[BB_LOG_CONNECTION]    = 'Connections and connection errors';
            $this->_log_level_description[BB_LOG_MODULE_ACTION] = 'Loading and unloading of modules';
            $this->_log_level_description[BB_LOG_MODULE_CALL]   = 'Modules called';
            $this->_log_level_description[BB_LOG_CHANNEL]       = 'Bot\'s joins and parts';
            $this->_log_level_description[BB_LOG_NOTICE]        = 'Notices';
            $this->_log_level_description[BB_LOG_MODULE]        = 'Modules';
            $this->_log_level_description[BB_LOG_ERROR]         = 'Errors';
        }
        /**
         * Prints to console description of what gets logged.
         */
        function printLogging()
        {
            if(count($this->loglevels)) {
                foreach($this->loglevels as $level) {
                    $this->addBotLog('## Logging enabled: ' . $this->_log_level_description[$level] . '.', BB_LOG_NONE, COLOR_GREEN);
                }
            } else {
                $this->addBotLog('## Logging disabled.', BB_LOG_NONE, COLOR_BLUE);
            }

        }

        /**
         * Log a message to the bot-log
         * @param string  $text
         * @param int     $level
         * @param int     $color see helpers::echoc()
         * @param boolean $newline add newline at the end
         */
        function addBotLog($text, $level, $output_color = 0, $newline = true)
        {
            if($text) {
                if(in_array($level, $this->loglevels) && $level > 0) {
                    $this->_bot->fileHandler->append($text, 'bot');
                }
                if($this->_output) {
                    if($this->_colored_output == 'yes') {
                        helpers::echoc(ltrim(date($this->console_date_format)) . ' ' . $text, $output_color, $newline);
                    } else {
                        echo ltrim(date($this->console_date_format)) . ' ' . $text . "\n";
                    }
                }
            } else {
                helpers::bugreport(__FILE__,__LINE__, 'addBotLog($text, $level) with empty $text');
            }
        }

        /**
         * Log a message to the channel-log
         * @param object $buffer
         */
        function channels($buffer)
        {
            $log = array();
            foreach($buffer->channels as $channel) {
                switch ($buffer->command) {
                    case PRIVMSG:
                        if(substr($buffer->text, 0, 6) == ACTION) {
                            $log[$channel] = 'Action: ' . $buffer->nick . ' ' . substr($buffer->text, 7);
                        } else {
                            $log[$channel] = '<' . $buffer->nick . '> ' . $buffer->text;
                        }
                        break;

                    case JOIN:
                        if(count($buffer->raw) == 3) {
                            if($buffer->nick == $this->server->bot_nick_name) {
                                $this->addBotLog('## Joined ' . $channel, BB_LOG_CHANNEL, COLOR_GREEN);
                                $log[$channel] = $buffer->nick . ' joined ' . $channel . '.';
                            } else {
                                $log[$channel] = $buffer->nick . ' (' . $buffer->identd . '@' . $buffer->hostname . ') joined ' . $channel . '.';
                            }
                        }
                        break;

                    case QUIT:
                        if($buffer->nick == $this->server->bot_nick_name) {
                               $this->addBotLog('## Quitting ' . $this->server->host, BB_LOG_CONNECTION, COLOR_YELLOW);
                        }
                        $log[$channel] = $buffer->nick . ' (' . $buffer->identd . '@' . $buffer->hostname . ') left irc: ' . $buffer->text;
                        break;

                    case NOTICE:
                        $log[$channel] = '-' . $buffer->nick . ':' . $channel . '- ' . $buffer->notice;
                        break;

                    case PART:
                        if($buffer->text != '') {
                            $partmsg = ' (' . $buffer->text . ').';
                        } else {
                            $partmsg = '.';
                        }
                        if($buffer->nick == $this->server->bot_nick_name) {
                           $this->addBotLog('## Parted ' . $channel, BB_LOG_CHANNEL, COLOR_YELLOW);
                        }
                        $log[$channel] = $buffer->nick . ' (' . $buffer->identd . '@' . $buffer->hostname . ') left ' . $channel . $partmsg;
                        break;

                    case MODE:
                        $log[$channel] = $channel . ": mode change '".$buffer->mode."' by ".$buffer->userhost;
                        break;

                    case KICK:
                        if($buffer->kicked == $this->server->bot_nick_name) {
                            $this->addBotLog('## Got kicked from ' . $channel, BB_LOG_CHANNEL, COLOR_RED);
                        }
                        $log[$channel] = $buffer->kicked.' kicked from ' . $channel . ' by ' . $buffer->nick . ': ' . $buffer->text;
                        break;

                    case NICK:
                        $log[$channel] = 'Nick change: ' . $buffer->nick . ' -> ' . $buffer->newnick;
                        break;

                    case TOPIC:
                        $log[$channel] = 'Topic changed on ' . $channel . ' by ' . $buffer->userhost .': ' . $buffer->topic;
                        break;
                }
            }
            if(count($buffer->channels) != 0) {
                foreach($buffer->channels as $channel) {
                    if(in_array($channel, $this->_log_channels)) {
                        if(isset($log[$channel])) {
                            if(substr($channel, 0, 1) == '#') {
                                $channelName = substr($channel, 1);
                            } else {
                                $channelName = $channel;
                            }
                            $this->_bot->fileHandler->append($log[$channel], $channelName, true);
                        }
                    }
                }
            }
            $buffer = null;
        }

         /**
         * Turn off all console output
         */
        function disableAllConsoleOutput()
        {
            $this->_output = false;
        }
    }
?>
