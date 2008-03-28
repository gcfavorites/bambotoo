<?php
    /**
     * Channels - list channels BOT is on
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Channels Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class channels extends module
	{
        var $version = '0.1';
		var $mode    =  ADMIN_MODE;
		var $trigger = 'ch';
		var $help    = 'Lists channels bot is monitoring';
        var $usage   = '.ch [<channels> or * for all] [+ for nicks]. eg .ch #foo #bar +';
        var $credits = 'Created by Pete "mash" Morgan <pedromorgan@gmail.com>, part of bambotoo. http://www.lejban.se/bambotoo/';
		var $unloadable = false;

		function call()
		{
            if(!$this->arguments) {
                $this->sendUsage();
            } else {
                $args = array_flip(array_slice($this->args,1,count($this->args)));
                $curr_channels = $this->channels->getChannelsAsArray();
                $channels = array();
                $list_all_users = array_key_exists('+',$args);
                $err = array();
                $err_str = '';
                if($list_all_users) {
                    unset($args['+']);
                }
                $list_all_channels = array_key_exists('*',$args);
                if($list_all_channels) {
                    $channels = $curr_channels;
                } else {
                    foreach($args as $chan => $foo) {
                         if($chan{0} != '#') {
                             $chan = '#' . $chan;
                         }
                         if(in_array($chan,$curr_channels)) {
                            $channels[] = $chan;
                         } else {
                             $err[] = $chan;
                         }
                    }
                    if(count($err) > 0) {
                       $err_str = '. {not found: '.implode(',',$err).'}';
                    }

                }
                $str = 'Channels: '.$this->channels->getChannelsAsString($channels, $list_all_users) . $err_str;
                $this->send($str);
            }
		}
	}
?>