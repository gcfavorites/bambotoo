<?php
    /**
     * insmod
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Module that inserts other modules into bambotoo
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class insmod extends module
	{
        var $version = '0.1-stable';
		var $mode    =  ADMIN_MODE;
		var $trigger = 'insmod';
        var $help    = 'Dynamically loads modules';
        var $usage   = '.insmod <modules to load>';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';
        var $unloadable = false;

        function call()
		{
			if($this->arguments) {
                list($cmd, $modules_to_load) = explode(' ', $this->buffer->text, 2);
                $return = $this->__bot->moduleHandler->loadModules($modules_to_load);
            } else {
                $return = $this->getUsage();
            }
			$this->send($return);
		}
	}
?>