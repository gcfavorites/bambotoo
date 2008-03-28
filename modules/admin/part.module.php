<?php
    /**
     * part
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Part Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class part extends module
	{
        var $version = '0.1';
		var $mode    = ADMIN_MODE;
		var $trigger = 'part';

		var $help    = 'Part a channel';
        var $usage   = '.part <channel> [<message>]';
		var $unloadable = false;
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

		function call()
		{
            $currentChannel = $this->buffer->channels[0];
            if($this->arguments) {
                $channelName = $this->args[1];
                if(isset($this->args[2])) {
                    array_shift($this->args);
                    array_shift($this->args);
                    $message = join($this->args, ' ');
                } else {
                    $message = '';
                }
        	    $this->sendRaw('PART ' . $channelName . ' :' . $message);
                if($currentChannel == $channelName) {
                    $currentChannel = $this->buffer->nick;
                }
                if($message) {
                    $this->sendTo('Left ' . $channelName . ' with part message: ' . $message, $currentChannel);
                } else {
                    $this->sendTo('Left: ' . $channelName, $currentChannel);
                }
            } else {
                $this->sendUsage();
            }

		}
	}
?>