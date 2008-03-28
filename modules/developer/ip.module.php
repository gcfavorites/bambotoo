<?php
    /**
     * ip
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * IP Module - Get the IP from a hostname
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     */
    class ip extends module
    {
        var $version = '0.1';
        var $trigger = 'ip';
        var $mode    = USER_MODE;
        var $help    = 'Get the IP from a hostname';
        var $usage   = '!ip <hostname>';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';
        
        function call()
        {
            $return = '';
            if(strpos($this->buffer->text, ' ') !== false) {
                list($cmd, $arg) = explode(' ', $this->buffer->text, 2);
                if(strpos($arg, ' ') !== false) {
                    $return = $this->getUsage();
                } else {
                    $return = gethostbyname($arg);
                }
            } else {
                $return = $this->getUsage();
            }
            $this->send($return);
        }
    }

?>
