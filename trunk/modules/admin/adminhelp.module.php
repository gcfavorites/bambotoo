<?php

    /**
     * adminhelp
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Adminhelp Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class adminhelp extends module
	{
        var $version = '0.1-stable';
		var $mode    = ADMIN_MODE;

		var $trigger = 'help';
		var $help    = 'Show help for admin';
        var $usage   = '.help <trigger>';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

		function call()
		{
            $modules = &$this->__bot->moduleHandler->loaded_modules;
            $help = '';
            if($this->arguments)
			{
				foreach($modules as $module)
				{
					if($module->enabled && $module->command == PRIVMSG && $module->mode == ADMIN_MODE && $module->trigger == 
$this->args[1])
					{
						$help = $module->help . ' ' . $module->usage;
						break(1);
					}
				}
				if(!$help)
					$help = 'Command "' . $this->args[1] . '" does not exist. ' . $this->getUsage();
			}
			else
			{
				foreach($modules as $module)
				{
					if($module->enabled && $module->command == PRIVMSG && $module->mode == ADMIN_MODE && $module->trigger != ALL)
						$help .= $module->trigger . ' ';
				}
				$help = trim($help) . '. Use "' . $this->__bot->admincmdchar . 'help <command>" to learn more.';
			}

			$this->send($help);

		}
	}
?>
