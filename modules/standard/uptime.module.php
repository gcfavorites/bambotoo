<?php
    /**
     * uptime.module.php
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-standard-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Uptime Class
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-standard-modules
     */
    class uptime extends module
    {
        var $version     = '0.1';
        var $credits     = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

        var $trigger     = 'uptime';
        var $help        = 'Shows uptime';
        var $description = 'Shows uptime and starttime';
        var $usage       = '!uptime';
        
        function call()
        {
            $up_in_secs = time() - ($this->__bot->start_time);
            $start_date_string = date('d M Y H:i:s',(time() - $up_in_secs));
            $return  = 'Uptime: '.helpers::getTimeDiff($start_date_string) . ' ';
            $return .= 'Started: '.date('D dS M Y H:i:s A');
            $this->send($return);
        }
    }
?>