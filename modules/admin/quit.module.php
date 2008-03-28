<?php
    /**
     * quit
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Quit Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class quit extends module
	{
        var $version = '0.1';
		var $mode    = ADMIN_MODE;
		var $trigger = 'quit';
		var $help    = 'Quit\'s IRC';
        var $usage   = '.quit <optional message>';
		var $unloadable = false;
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

		function call()
		{
            if($this->arguments) {
                array_shift($this->args);
                $quitmsg = join($this->args, ' ');
            } else {
                $quitmsg = 'http://www.lejban.se/bambotoo/';
            }
			$this->sendRaw('QUIT :' . $quitmsg);
			$this->__bot->quit = true;
		}
	}
?>