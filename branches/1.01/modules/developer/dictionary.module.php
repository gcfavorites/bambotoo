<?php
    /**
     * Dictionary
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Dictionary Module - Looks up a dictionary word
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     */
    class dictionary extends module
    {
        var $version = '0.3-stable';
        var $trigger = 'dict';
        var $help    = 'Looks up a dictionary word';
        var $usage   = '!dict <word>';
        var $credits = 'Created by "mash" Morgan <pedromorgan@gmail.com>, part of bambotoo. http://www.lejban.se/bambotoo/';

        function call()
        {
            $url = $this->config['url'];
            $search_script = $this->config['search_script'];
            if(strpos($this->buffer->text, ' ') !== false) {
                list($cmd, $arg) = explode(' ', $this->buffer->text, 2);
                if(strlen(trim($arg)) < 3) {
                    $foo = $this->getUsage() . ' - Word is too short ';
                } else {
                    $foo = 'Dictionary lookup : ['.$arg . '] '.$url.$search_script.urlencode($arg);
                }
            } else {
                $foo = $this->getUsage() . ' - Dictionary reference at '.$url;
            }
            $this->send($foo);
        }
    }

?>
