<?php
    /**
     * userhelp
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-standard-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Userhelp Module
     * @package    bambotoo-modules
     * @subpackage bambotoo-standard-modules
     */
	class userhelp extends module
	{
		var $version = '0.2-stable';
		var $trigger = 'help';
        var $mode    = USER_MODE;

		var $help    = 'Haha... really funny. ;)';
        var $usage   = '!help <command>';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo';

		function call()
		{
			$help = '';

			if(count($this->args) > 1)
			{
				foreach($this->__bot->moduleHandler->loaded_modules as $module)
				{
					if($module->enabled && $module->command == PRIVMSG && $module->mode == USER_MODE && $module->trigger == $this->args[1])
					{
						$help = $module->help . '. ' . $module->getUsage();
						break(1);
					}
				}
				if(!$help)
					$help = 'Command "' . $this->args[1] . '" does not exist. ' . $this->getUsage();
			}
			else
			{
                ksort($this->__bot->moduleHandler->loaded_modules);
				foreach($this->__bot->moduleHandler->loaded_modules as $module)
				{
					if($module->enabled && $module->command == PRIVMSG && $module->mode == USER_MODE && $module->trigger != ALL)
						$help .= $module->trigger . ' ';
				}
				$help = trim($help) . ' ' . $this->getUsage();
			}
			$this->send($help);

		}
	}
?>
