<?php
    /**
     * hostname
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Hostname Module - Get the Hostname for a IP
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     */
    class hostname extends module
    {
        var $version = '0.1-stable';
        var $trigger = 'hostname';
        var $help    = 'Get the Hostname for a IP';
        var $usage   = '!hostname <ip>';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

        function call()
        {
            $return = '';
            if(strpos($this->buffer->text, ' ') !== false) {
                list($cmd, $arg) = explode(' ', $this->buffer->text, 2);
                if(strpos($arg, ' ') !== false) {
                    $return = $this->getUsage();
                } else {
                    $return = gethostbyaddr($arg);
                    if($return == $arg || $return == '') {
                        $return = 'Could not resolv ' . $arg;
                    }
                }
            } else {
                $return = $this->getUsage();
            }
            $this->send($return);
        }
    }

?>
