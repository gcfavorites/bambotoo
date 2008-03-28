<?php
    /**
     * rmmod
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Rmmod Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class rmmod extends module
	{
        var $version = '0.1';
		var $mode    = ADMIN_MODE;
		var $trigger = 'rmmod';
		var $help    = 'Unloads modules';
        var $usage   = '.rmmod <modules list>';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';
		var $unloadable = false;

		function call()
		{
			 if($this->arguments) {
			     list($text, $modules_string) = explode(' ', $this->buffer->text, 2);
			     $return = $this->__bot->moduleHandler->unloadModules($modules_string);
             } else {
                $return = $this->getUsage();
             }
             $this->send($return);
		}
	}
?>