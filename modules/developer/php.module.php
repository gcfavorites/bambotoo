<?php // VERIFIED
    /**
     * php
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * PHP Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     */
    class php extends module
    {
        var $version = '0.2-stable';
        var $trigger = 'php';
        var $help    = 'Links to a PHP manual entry';
        var $usage   = '!php <item>';
        var $credits = 'Created by Pete "mash" Morgan <pedromorgan@gmail.com>, part of bambotoo. http://www.lejban.se/bambotoo/';
        
        function call()
        {
            $return = '';
            if(strpos($this->buffer->text, ' ') !== false) {
                list($cmd, $arg) = explode(' ', $this->buffer->text, 2);
                if(strlen(trim($arg)) < 4) {
                    $return = $this->getUsage() . ' - String is too short ';
                } else {
                    $return = 'PHP manual: ['.$arg . '] ' . $this->config['php_url'] . $this->config['search_script'] . $arg;
                }
            } else {
                $return = $this->getUsage() . ' -  PHP manual is at ' . $this->config['php_url'] . 'manual/en/';
            }
            $this->send($return);
        }
    }

?>
