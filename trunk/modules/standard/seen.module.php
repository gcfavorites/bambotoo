<?php
    /**
     * seen
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-standard-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Seen Module
     * @package    bambotoo-modules
     * @subpackage bambotoo-standard-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     */
    class seen extends module
    {
        var $version = '0.1-dev';
        var $trigger = 'seen';
        var $mode    = USER_MODE;

        var $help    = 'Find last time a user spoke or left';
        var $usage   = 'seen <nick>';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo';

        /**
         * @access private
         * @var int
         */
        var $_offset;

        /**
         * @access private
         * @var string
         */
        var $_logdir;

        function init()
        {
            $this->_logdir = $this->__bot->config['log_dir'];
            if($this->__bot->config['log_date_time_format'] != '[H:i]') {
                $this->enabled = false;
                $this->addBotLog('!! [seen] module not loaded: The configuration option "log_date_time_format" need to be in the format "[H:i]"', BB_LOG_ERROR, COLOR_RED);
            }
        }

        function call()
        {
            $return = false;
            if($this->arguments) {
                if(isset($this->args[2])) {
                    $this->sendUsage();
                } else {
                    $this->_offset = 1;
                    $this->_seen($this->args[1], $this->buffer->channels[0]);
                }
            } else {
                $this->sendUsage();
            }
        }

        function _seen($nickname, $channel)
        {
            $lines = true;
            while($lines) {
                $lines = $this->getNextFile($channel);
                for($i = (count($lines) -1); $i >= 0; $i--) {
                    if(preg_match('/^.*<' . preg_quote($nickname) . '>.*$/s', $lines[$i])) { // Match privmsg here
                        $this->sendResponse($nickname, $channel, $lines[$i], PRIVMSG);
                        return;
                    } elseif(preg_match('/.*' . preg_quote($nickname) . '.*\(.*\) left irc: .*/s', $lines[$i])) { // match quit here.
                        $this->sendResponse($nickname, $channel, $lines[$i], QUIT);
                        return;
                    } elseif(preg_match('/.*' . preg_quote($nickname) . '.*\(.*\) left ' . $channel . '.*/s', $lines[$i])) { // match part here.
                        $this->sendResponse($nickname, $channel, $lines[$i], PART);
                        return;
                    }
                }
            }
            $this->send('I haven\'t seen ' . $nickname . '.');
        }

        function getNextFile($channel)
        {
            $this->_offset--;
            if($channel[0] == '#') {
                $channel = substr($channel, 1);
            }
            $file = $this->_logdir . 'channels/' . $channel . '/' . $channel . '.log.' . helpers::getDate(date("dMY"), $this->_offset);
            if(is_readable($file)) {
                return file($file);
            }
            else {
                return false;
            }
        }

        function getTimeStamp($line) {
            return substr($line, 1, 5);
        }

        function sendResponse($nickname, $channel, $line, $type)
        {
            $timestamp = $this->getTimeStamp($line);
            $date = helpers::getDate(date("dMY"), $this->_offset);
            $timeDiff = helpers::getTimeDiff($date . ' ' . $timestamp);
            switch($type) {
                case PRIVMSG:
                    $this->send($nickname . ' was last talking in ' . $channel . ' ' . $timeDiff . ' ago.');
                    break;
                case PART:
                    $this->send($nickname . ' left ' . $channel . ' ' . $timeDiff . ' ago.');
                    break;
                case QUIT:
                    $this->send($nickname . ' left irc ' . $timeDiff. ' ago');
                    break;
            }
        }
    }
?>
