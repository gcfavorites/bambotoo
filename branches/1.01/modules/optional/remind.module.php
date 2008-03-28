<?php
    /**
     * remind
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Remind Module
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     */
    class remind extends module
    {
        var $version = '0.1';
        var $trigger = 'remind';
        var $help    = 'Set a reminder. use time as +1min eg !remind +20min Pizza is ready';
        var $usage   = '!remind <time> <text> eg !remind +20min Pizza is ready';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

        function call()
        {
            list($cmd, $time, $text) = explode(' ', $this->buffer->text, 3);
            if($time && $text) {
                $rTime = strtotime($time);
                if($rTime > 0) {
                    $this->send('Reminding you in ' . helpers::getTimeDiff($time, true) . '.');
                    $this->send($this->buffer->nick . ': ' . $text, $rTime);
                } else {
                    $this->send($this->buffer->nick . ': Unknown time.');
                }
            } else {
                $this->sendUsage();
            }

        }
    }
?>
