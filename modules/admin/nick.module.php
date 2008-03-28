<?php
    /**
     * nick
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Nick Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class nick extends module
	{
        var $version = '0.1';
		var $mode    = ADMIN_MODE;
		var $trigger = 'nick';
		var $help    = 'Change the bot\'s nick';
        var $usage = '.nick <newnick>';
		var $unloadable = false;
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

		function call()
		{
             if($this->arguments) {
                list($text, $newnick) = explode(' ', $this->buffer->text, 2);
                if(strlen($newnick) == 0) {
                    $this->sendUsage();
                } elseif(strlen($newnick) > $this->server->settings['MAXNICKLEN']) {
                	$this->send('Nick is too long. Max is ' . $this->server->settings['MAXNICKLEN'] . ' characters.');
                } else {
                	for($i = 0; $i < strlen($newnick); $i++)
                	{
                		$ascii = ord($newnick{$i});
                		if((($ascii > 64 && $ascii < 90) || ($ascii > 96 && $ascii < 172) || $ascii == 174 || $ascii == 136) == false)
                		{
                			$this->send('The character "' . $newnick{$i} . '" is not allowed. (A-Z, a-z, ^ and | are allowed');
                			unset($newnick);
                		}
                	}
                	if($newnick) {
                		$this->sendRaw('NICK :' . $newnick);
                    }
                }
             } else {
                 $this->sendUsage();
             }
		}
	}
?>