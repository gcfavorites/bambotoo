<?php

    /**
     * Admin
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Admin Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class admin extends module
	{
		var $trigger    = 'admin';
        var $version    = '0.1-stable';

		var $help       = 'Authenticate as an admin with the bambotoo';
        var $usage      = '!admin <password>';
		var $unloadable = false;
        var $credits    = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';
        

        var $lastTry    = 0;

        function init()
        {
            if($this->server->admin_pass == '') {
                die("Error: No admin password set in configuration.\n");
            }
        }
		function call()
		{
            $time = time();
            if(substr($this->buffer->channels[0], 0, 1) == '#') {
                $return = 'You need to the this command as a private message to me.';
            } else {
                if(in_array($this->buffer->userhost, $this->server->admins)) {
                    $return = 'You are already authenticated as an admin. Use .help to see available admin commands.';
                } elseif($this->lastTry < $time - 10) {
                    $this->lastTry = $time;
                    if($this->arguments) {
            			if($this->server->admin_pass == $this->args[1]) {
                            $this->server->admins[] = $this->buffer->userhost;
            				$return = 'You are now authenticated as an admin. Use .help to see available admin commands.';
            				$this->addBotLog('## ' . $this->buffer->userhost . ' authed.', BB_LOG_NOTICE);
            			} else {
            				$return =  'That\'s so wrong...';
            			}
                    } else {
                        $return = $this->getUsage();
                        $this->lastTry = 0;
                    }
                } else {
                    $return = 'Tries too close, please wait ' . strval(($this->lastTry + 10) - $time) . ' seconds.';
                }
            }
            $this->send($return);
		}
	}
?>