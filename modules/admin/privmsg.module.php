<?php
    /**
     * privmsg
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Privmsg Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class privmsg extends module
	{
        var $version = '0.2';
		var $mode    = ADMIN_MODE;
		var $trigger = 'say';
		var $help    = 'Makes me talk';
        var $usage   = 'say #channel <text>';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

		function call()
		{
            $args = explode(' ', $this->buffer->text, 3);
			if(isset($args[2])) {
				$this->sendTo($args[2], $args[1]);
            } else {
				$this->sendUsage();
            }
		}
	}
?>
