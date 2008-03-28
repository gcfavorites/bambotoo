<?php
    /**
     * lsmod
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom, Pete "mash" Morgan
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Lsmod Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-admin-modules
     */

	class modman extends module
	{
        var $version = '0.2-stable';
		var $mode    = ADMIN_MODE;
		var $trigger = 'modman';

		var $help    = 'Module manager';
        var $usage   = '.modman [ av, ls, nl, load <mods> , unload <mods list> ]';
		var $unloadable = false;
        var $credits = 'Created by Pete "mash" Morgan, part of bambotoo. http://www.lejban.se/bambotoo/';
        
		function call()
		{
			$return = '';
            $modHandler = &$this->__bot->moduleHandler;

            if(!$this->arguments){
                $return = $this->getUsage();
            }else{
                $cmd = $this->args[1];
                switch($cmd){
                    case 'loaded':
                    case 'ls':
                        $return = $modHandler->listLoadedModules();
                        break;

                    case 'avail':
                    case 'av':
                         $return = $modHandler->listAvailableModules();
                        break;

                    case 'notloaded':
                    case 'nl':
                        $return = $modHandler->listModulesNotLoaded();
                        break;

                    case 'load':
                    case 'unload':
                        if(count($this->args) <= 2){
                            $return = 'Error: no module(s) to '.$cmd.'. Usage: .modman '.$cmd.' <modules list>. ';
                        }else{
                            $mod_list = helpers::toArray($this->buffer->text,3);
                            if($cmd == 'load'){
                                $return = $modHandler->loadModules($mod_list[2]);
                            }else{ // unload
                                $return = $modHandler->unloadModules($mod_list[2]);
                            }
                        }
                        break;

                    default:
                        $return = 'Command option "'.$this->args[1].'" not found. '.$this->getUsage();
                }
            }

			$this->send($return);
		}
	}
?>