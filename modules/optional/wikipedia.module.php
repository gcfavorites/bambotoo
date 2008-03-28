<?php

    /**
     *  Wikipedia
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Wikipedia Module - Links to wikipedia search
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author Pete "mash" Morgan <pedromorgan@gmail.com>
     */

    class wikipedia extends module
    {
        var $version = '0.2';
        var $trigger = 'wikipedia';

        var $help    = 'Links to wikipedia search';
        var $usage   = '!wikipedia <search string>';
        var $credits = 'Created by Pete "mash" Morgan <pedromorgan@gmail.com>, part of bambotoo. http://www.lejban.se/bambotoo/';

        function call()
        {
            $home_page = $this->config['home_page'];
            $search_url = $this->config['search_url'];
            if(strpos($this->buffer->text, ' ') !== false) {
                list($cmd, $arg) = explode(' ', $this->buffer->text, 2);
                if(strlen(trim($arg)) < 3){
                    $foo = 'Usage: !wikipedia <search string> - String is too short ';
                }else{
                    $foo = 'Wikipedia: [ '.$arg.' ] ' . $search_url.urlencode($arg);
                }
                $this->send($foo);
            } else {
                ## TODO: give the html title
                $this->sendUsage();
            }

        }
    }

?>