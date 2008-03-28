<?php // VERIFIED
    /**
     * about
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author     Peter "mash" Morgan <pedromorgan@gmail.com>
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * About Module - Details of this bot
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     */
    class about extends module
    {
        var $version = '0.2';
        var $trigger = 'about';

        var $help    = 'About Bambotoo or a module, use -d for a longer description of a module';
        var $usage   = '!about [-d] [<module>]';
        var $credits = 'Created by Peter Morgan and Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

        function call()
        {
            $return = '';
            $desc = false;
            $moduleName = false;
            if($this->arguments) {
                if(count($this->args) == 3) {
                    if($this->args[1] == '-d') {
                        $desc = true;
                        $moduleName = $this->args[2];
                    } elseif($this->args[2] == '-d') {
                        $desc = true;
                        $moduleName = $this->args[1];
                    }
                } elseif(count($this->args) == 2) {
                    $moduleName = $this->args[1];
                }
                if($moduleName) {
                    $moduleHandler = &$this->__bot->moduleHandler;
                    if($moduleHandler->isModule($moduleName)) {
                        if($desc) {
                            if(isset($moduleHandler->loaded_modules[$moduleName]->description)) {
                                $return = 'Description for ' . $moduleName . ': ' . $moduleHandler->loaded_modules[$moduleName]->description;
                            } else {
                                $return = 'No description for ' . $moduleName;
                            }
                        } else {
                            if(isset($moduleHandler->loaded_modules[$moduleName]->credits)) {
                                $return = 'About ' . $moduleName . ': ' . $moduleHandler->loaded_modules[$moduleName]->credits;
                            } else {
                                
                                $return = 'No credits for ' . $moduleName;
                            }
                        }
                    } else {
                        $return = 'Module "' . $moduleName . '" not found';
                    }
                }
            } else {
                $return = APP_NAME . ', IRC Bot written in PHP.'
                          .' Version: ' . APP_VERSION . ' '
                          .' Url: http://www.lejban.se/bambotoo/ '
                          .' License: GPL.';
            }
            $this->send($return);
        }
    }

?>
