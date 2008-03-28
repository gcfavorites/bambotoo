<?php
    /**
     * lsmod
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Lsmod Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class lsmod extends module
	{
        var $version = '0.1-stable';
		var $mode    = ADMIN_MODE;
		var $trigger = 'lsmod';

		var $help    = 'List loaded modules';
        var $usage   = '.lsmod';
		var $unloadable = false;
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

		function call()
		{
			$return = $this->__bot->moduleHandler->listLoadedModules();
			$this->send($return);
		}
	}
?>