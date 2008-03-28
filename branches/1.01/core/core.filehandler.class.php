<?php // VERIFIED
    /**
     * Handles files
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */

    /**
     * Filehandler Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class filehandler
    {
        /**
         * Directory where log files are contained
         * @var string
         * @access private
         */
        var $_logdir;

        /**
         * Flag to indicate whether current log file is open for write
         * @var boolean
         * @access private
         */
        var $_file_open_for_write = array();

        /**
         * File pointer for the current log file
         * @var integer
         * @access private
         */
        var $_fp = array();

        /**
         * Name of the current file. eg /full/path/bambotoo.2007-12-25.log
         * Please note that this changes with date. When the date rolls over
         * to next day  the current file is closed and new log file created
         * @var string
         * @access private
         */
        var $_current_file = array();

        /**
         * The string used to format the date of the log file name
         * This variable is configurable in the bambotoo.conf file's 'log_date_time_format' setting
         * Use's the PHP's date() function.
         * eg 'Y-m-d' = '2007-12-25' for a daily log or 'Y-m-d:H' for hourly log
         * @var string
         * @access private
         */
        var $_log_filename_date_format;

        /**
         * The string used to format the date with php's date() function within log files
         * This variable is configurable in the bambotoo.conf file's 'log_date_time_format' setting
         * eg 'Y-m-d' = '2007-12-25' for a daily log or 'Y-m-d:H' for hourly log
         * @var string
         * @access private
         */
        var $_log_date_time_format;

        /**
         * The date part of the file as a string, which is checked against on each call
         * to current time eg '2007-12-25'
         * @var string
         * @access private
         */
        var $_file_date_part = null;

        /**
         * Contains the current date of each hit to append, ie only one call to date()
         * just incase the second changes between a call
         * @var string
         * @access private
         */
        var $_hit_date = null;

        /**
         * @var array
         */
        var $_dir_exists;

        /**
         * Filehandler constructor
         * @param string $config Reference to the bot's config
         */
        function filehandler(&$config)
        {
            $this->_logdir = &$config['log_dir'];
            $this->_log_filename_date_format = &$config['log_filename_date_format'];
            $this->_log_date_time_format = &$config['log_date_time_format'];
            $this->_checkLogDirs();
        }

        /**
         * opens a file for logging. Called from append()
         * @param string  $prefix        the "channels" to log and also the "key" for the array of open files
         * @param boolean $is_channel    creates file in channels/ subdir
         * @param boolean $close_current flag to close the file as name is changing
         */
       function _openFile($prefix, $is_channel, $close_current = false)
        {
            if($close_current) {
                fclose($this->_fp[$prefix]);
            }
            $this->_file_date_part = $this->_hit_date;
            // channel files are in channels/ subdir
            if($is_channel) {
                $this->_current_file[$prefix] = $this->_logdir . 'channels/' . $prefix . '/' . $prefix . '.log.' . $this->_file_date_part;
            } else {
                $this->_current_file[$prefix] = $this->_logdir . $prefix . '/' . $prefix . '.log.' . $this->_file_date_part;
            }

            $this->_fp[$prefix] = fopen($this->_current_file[$prefix], 'ab');
            $this->_file_open_for_write[$prefix] = true;
        }

        /**
         * append text to a log file
         * @param string $log_text text to append to file
         * @param string $prefix   the "channels" to log and also the "key" for the array of files
         * @param boolean $is_channel   flag that indicates channel, files in the channels/ subdir
         */
        function append($log_text, $prefix, $is_channel = false)
        {
            $log = date($this->_log_date_time_format).' '.$log_text."\n";
            $this->_hit_date = date($this->_log_filename_date_format);
            if(!isset($this->_file_open_for_write[$prefix])) {
                if(!isset($this->_dir_exists[$prefix])){
                    $this->_check_dir_exists($prefix, $is_channel);
                }
                $this->_openFile($prefix, $is_channel);
            } elseif($this->_file_date_part != $this->_hit_date) {
                $this->_openFile($prefix,$is_channel, true);
            }
            fwrite($this->_fp[$prefix], $log);
        }

        /**
         * Checks directories exists and if not creates them. Sets the $_dir_exists flag for the item
         * @param string  $prefix       the "channels" to log and also the "key" for the array of files
         * @param boolean $is_channel   flag that indicates channel, files in the channels/ subdir
         * @access private
         */
        function _check_dir_exists($prefix, $is_channel) {
            if($is_channel) {
                $subdirName = $this->_logdir . 'channels/';
            } else {
                $subdirName = $this->_logdir;
            }
            $dirName = $subdirName . $prefix;
            if(!file_exists($dirName)) {
                if(is_writable($subdirName) && !is_dir($dirName)) {
                    $success = mkdir($dirName);
                } else {
                    $success = false;
                }
            } else {
                $success = true;
            }
            
            if($success) {
                $this->_dir_exists[$prefix] = true;
            }
        }
        /**
         * Checks whether the log dir(s) exists and are writable
         * @access private
         */
        function _checkLogDirs()
        {
            if(substr($this->_logdir, -1) != '/') { // make sure of trailing slash
                $this->_logdir .= '/';
            }
            if(!is_dir($this->_logdir)) {
                die("\nFatal error: The log directory '" . $this->_logdir . "' does not exist\n\n");
            }
            if(!is_writable($this->_logdir)) {
                die("\nFatal error: The log directory '" . $this->_logdir . "' exists but is not writable by user '" . $_ENV['USER'] ."'\n\n");
            }

            /*
             * check required bot sub directory exits
             */
            if(!file_exists($this->_logdir . 'bot')) {
                mkdir($this->_logdir . 'bot');
            }
            /*
             * check required channels sub directory exits
             */
            if(!file_exists($this->_logdir . 'channels')) {
                mkdir($this->_logdir . 'channels');
            }
        }
        /**
         * Search a file for a string
         * @static
         * @param string $file
         * @param string $text
         * @return string The match
         */
        function search($file, $text)
        {
            $lines = file($file);
            foreach($lines as $line) {
                if(fnmatch($line, $text)) {
                    return $line;
                }
            }
            return false;
        }
        /**
         * Search a file backwards for a string
         * @static
         * @param string $file
         * @param string $text
         * @return string The match
         */
        function rsearch($file, $text)
        {
            $lines = file($file);
            for($i = count($lines); $i >= 0; $i--) {
                if(fnmatch($lines[$i], $text)) {
                    return $lines[$i];
                }
            }
            return false;
        }
    }
?>