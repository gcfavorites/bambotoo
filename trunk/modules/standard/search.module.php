<?php
    /**
     * search
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-standard-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Search Module
     * @package    bambotoo-modules
     * @subpackage bambotoo-standard-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     */
    class search extends module
    {
        var $version = '0.2';
        var $trigger = 'search';
        var $mode    = USER_MODE;

        var $help    = 'Search logs';
        var $usage   = '!search <string>';
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
        }

        function call()
        {
            $return = false;
            if($this->arguments) {
                array_shift($this->args);
                $search = join($this->args, ' ');
                $this->_offset = 1;
                $this->_search($search, $this->buffer->channels[0]);
            } else {
                $this->sendUsage();
            }
        }

        function _search($searchString, $channel)
        {
            //echo "_search($searchString, $channel)\n";
            $lines = true;
            while($lines) {
                $lines = $this->getNextFile($channel);
                for($i = count($lines); $i >= 0; $i--) {
                    if(stristr($lines[$i], $searchString)) { // Match here
                        $this->send($lines[$i]);
                        return;
                    }
                }
            }
            $this->send('No hit.');
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
    }
?>
