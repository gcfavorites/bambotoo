<?php
    /**
     * join
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Join Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class join extends module
	{
        var $version = '0.2-stable';
		var $mode    =  ADMIN_MODE;
		var $trigger = 'join';

		var $help    = 'Join a channel. Usage .join <#channels list>';
        var $usage   = '.join <#channels list>';
        var $credits = 'Created by Tobias Nystrom and Pete "mash" Morgan, part of bambotoo. http://www.lejban.se/bambotoo/';
		var $unloadable = false;

		function call()
		{
             $str = $err_str = '';
             if($this->arguments) {
                 list($cmd, $channels_string) = explode(' ', $this->buffer->text, 2);
                 $channels_to_join = helpers::toArray($channels_string);
                 $curr_channels = $this->channels->getChannelsAsArray(); //array_keys($this->channels);
                 $chans = array();
                 $err_exists = array();

                 foreach($channels_to_join as $chan) {
                     $chan = trim($chan);
                     if($chan != '') {
                         if(substr($chan,0,1) != '#') {
                             $chan = '#'.$chan;
                         }
                         if(in_array($chan, $curr_channels) && !in_array($chan,$err_exists)) {
                             $err_exists[] = $chan;
                         } else {
                            $chans[] = $chan;
                            $curr_channels[] = $chan;
                         }
                    }
                 } // foreach $channels_ to_join

                if(count($err_exists)> 0){
                    $err_str = 'Ignored: '.implode(', ',$err_exists);
                }
                if(count($chans) > 0){
                    $str = 'Joined: '.implode(', ',$chans);
                    foreach($chans as $chan){
                        $this->sendRaw(CMD_JOIN . $chan);
                    }
                }
                if($str && $err_str) {
                    $err_str = ' - ' . $err_str;
                }
                $this->send($str.$err_str);

			} else {
			    $str = 'No channels defined. '.$this->getUsage();
                $this->sendRaw($str);
			}

		}
	}
?>