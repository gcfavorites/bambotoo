<?php // VERIFIED

    /**
     * Helper functions
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Helpers Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
	class helpers
	{
        /**
         * Returns the current microtime as a float
         * @access public
         * @static
         * @return float
         */
		function microtimeFloat()
		{
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
		}
		/**
         * Calculates dates
         * @access public
         * @static
         * @param string $date   The date in any format strtotime() understands
         * @param int    $offset The difference in days
         * @return string        date formatted as 03Jun2006
		 */
		function getDate($date, $offset)
		{
			$nextday   = date('d',strtotime($date)) + $offset;
			$nextmonth = date('m',strtotime($date));
			$nextyear  = date('Y',strtotime($date));
			$next = date('dMY',mktime(0, 0, 0, $nextmonth, $nextday, $nextyear));
			return $next;
		}

        /**
         * return uptime from a timestamp eg 1 day, 25 mins etc
         * @access public
         * @static
         * @param string $start_time Unix timestamp
         */
        function getUptimeString($start_time){
            $time_now = time();
            $up_in_secs = $time_now - $start_time;
            $start_date_string = date('d M Y H:i:s',($time_now - $up_in_secs));
            return helpers::getTimeDiff($start_date_string );
        }

		/**
         * Creates and returns a human readable string containing the difference in time compaired to now.
         * @access public
         * @static
         * @param  string  $date   The date in any format strtotime() understands
         * @param  boolean $future
         * @return string
		 */
		function getTimeDiff($date, $future = false)
		{
			$return = '';
			$date = strtotime($date);
            $currentTime = time();
            if($future) {
                $diff = $date - $currentTime;
            } else {
                $diff = $currentTime - $date;
            }
            if($diff > BAMBOTOO_SECS_IN_DAY) {
				$day = floor($diff / (BAMBOTOO_SECS_IN_DAY));
				$diff = $diff % (BAMBOTOO_SECS_IN_DAY * $day);
                if(intval($day) == 1) {
                    $return = intval($day).' day, ';
                } else {
                   $return = intval($day).' days, ';
                }

			}
			if($diff > BAMBOTOO_SECS_IN_HOUR) {
				$hour = floor($diff / BAMBOTOO_SECS_IN_HOUR);
				$diff = $diff % (BAMBOTOO_SECS_IN_HOUR  * $hour);
				if(intval($hour) == 1) {
				   $return .= intval($hour) . ' hour, ';
				} else {
				   $return .= intval($hour) . ' hours, ';
				}
			}
			if($diff >= BAMBOTOO_SECS_IN_MINUTE) {
				$min = floor($diff / BAMBOTOO_SECS_IN_MINUTE);
				if(intval($min) == 1) {
				    $return .= intval($min) . ' minute ';
				} else {
				    $return .= intval($min) . ' minutes ';
				}
			}
            if($return) {
                return substr($return, 0, -2);
            } else {
                return 'less than a minute';
            }
		}

        /**
         * Check if x _really_ is an int
         * @access public
         * @static
         * @param mixed $x var to test
         * @return boolean
         */
		function isRealInt($x)
		{
			if(!is_numeric($x)) {
				return false;
            } elseif(intval($x) == $x) {
				return true;
            } else {
				return false;
            }
		}

        /**
         * Makes an array from string seperated by spaces
         * (does an idiot check for empty elements eg multispaces in string)
         * @access public
         * @static
         * @param string $str - text make into array
         * returns array
         */
        function toArray($raw_txt, $limit = 100)
        {
            $txt = trim($raw_txt);
            $arr = array();
            if($txt != '') {
                $foo = explode(' ',$txt, $limit);
                foreach($foo as $v) {
                    if(trim($v) != '') {
                        $arr[] = trim($v);
                    }
                }
            }
            return $arr;
        }

        /**
         * Catch errors parsing ini file
         * @access public
         * @static
         */
        function get_ini_vars($file, $with_sections = false)
        {
            $arr = @parse_ini_file($file, $with_sections);
            if($arr) {
                return $arr;
            } else {
                die("Fatal error: Config file '" .$file."' is malformed!");
            }
        }

        /**
         * Returns an array as a single line string - mimicks print_r()
         * @param array $arr
         * @return string The array as a string
         */
        function print_r($arr)
        {
            $str = '';
            foreach($arr as $k => $v) {
                if(is_array($v)) {
                    $str .= "[$k]=array(".helpers::print_r($v).') ';
                } else {
                    $str .= "[$k]=>$v ";
                }
            }
            return $str;
        }
        
        /**
         * Delete empty array values
         * @param array $arr
         * @return array
         */
        function rm_empty_array($arr)
        {
            foreach($arr as $k => $v) {
                if(is_array($v)) {
                    $arr[$k] = helpers::rm_empty_array($arr[$k]);
                } elseif(!$v && $v != "0") {
                    unset($arr[$k]);
                }
            }
            return $arr;
        }
        
        /**
         * Bugreport function
         * call this when a check fails when it shouln't
         * call like this:
         * helpers::bugreport(__FILE__,__LINE__, 'Error-text here.');
         * 
         * @static
         * @param string $file
         * @param int    $line
         * @param string $str
         */
        function bugreport($file, $line, $str)
        {
            echo "\n ### BUG START ###\n";
            echo "  File: " . $file . "\n";
            echo "  Line: " . $line . "\n";
            echo " Error: " . $str . "\n";
            echo " ### BUG END ###\n\n";
            echo " This is a bug!\n Please report it to Bambotoos bugzilla @ http://bugs.lejban.se\n\n";
        }
        /**
         * Check if a hostname accually is a IP-number
         * @static
         * @param string $hostname
         * @return boolean true if hostname is and ip
         */
        function is_ip($hostname)
        {
            $ip = explode('.', $hostname);
            if(count($ip) != 4) {
                return false;
            }
            $block4 = array_pop($ip);
            foreach($ip as $block) {
                if(!is_numeric($block) || $block > 255 || $block < 1) {
                    return false;
                }
            }
            if(!is_numeric($block4) || $block4 > 255 || $block4 < 0) {
                    return false;
            }
            return true;
        }
        /**
         * Check if a string is a valid http URL
         * @static
         * @param string $hostname
         * @return boolean true if valid
         */
        function is_http_url($string)
        {
            if(preg_match( '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}((:[0-9]{1,5})?\/.*)?$/i' ,$string)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Convert a userhost to smallhost
         * @static
         * @param string $userhost
         * @return string smallhost
         */
        function toSmallhost($userhost)
        {
        	$posLastDot = strrpos($userhost, '.');
            $posExcl    = strpos($userhost, '!');
            $posAt      = strpos($userhost, '@');
            $hostname   = substr($userhost, $posAt + 1);
            if(helpers::is_ip($hostname)) {
                $userhost = explode('.', $userhost);
                array_pop($userhost);
                return join($userhost, '.') . '*';
            } else {

                $smallhost       = substr($userhost, 0, $posExcl) . '@*';
                $posNextLastDot  = strrpos(substr($hostname, 0, - (strlen($hostname) - $posLastDot)), '.');
                $smallhost      .= substr($hostname, $posNextLastDot);
                return $smallhost;
            }
        }

         /**
         * Gets a list of files in a dir
         * @static
         * @param string $dir - directory to scan
         * @return array - alpha sorted
         */
        function getFiles($target_dir, $ignore_hidden = true)
        {
            $files = array();
            $dir_handle = dir($target_dir);
            while (false !== ($file_name = $dir_handle->read())) {
                if($file_name == '.' || $file_name == '..' || substr($file_name,0,1) == '.'){
                    // ignore
                }else{
                    $files[] = $file_name;
                }
            }
            asort($files);
            return $files;
        }
        
        /**
         * Print colored output
         * 
         * @param string  $text     Text to color
         * @param int     $color    Use defined colors in core.constants.php
         * @param boolean $newline
         */
        function echoc($text, $color = 0, $newline = true)
        {
            if($newline) {
                $newline = "\n";
            } else {
                $newline = '';
            }
            if($color == 1 || $color == 4 || $color == 7 || ($color > 30 && $color < 38) || ($color > 40 && $color < 47)) {
                echo chr(27) . '[01;' . $color . ' m' . $text . chr(27) . '[00m' . $newline;
                return;
            } elseif($color != 0) {
                helpers::bugreport(__FILE__,__LINE__, 'Wrong color "' . $color . '" in used in echoc().');
            }
            echo $text . $newline;
        }
        
    }
    
    /**
     * Trims every item in an array recursively
     * 
     * This is used in the core.buffer.class.php
     * 
     * @param array $totrim
     * @return array The trimmed array
     */
    function trim_array($totrim)
    {
        if (is_array($totrim)) {
            $totrim = array_map('trim_array', $totrim);
        } else {
            $totrim = trim($totrim);
        }
        return $totrim;
    }
?>
