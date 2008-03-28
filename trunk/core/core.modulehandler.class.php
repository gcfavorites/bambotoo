<?php // VERIFIED

    /**
     * Handles modules
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Modulehandler Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class modulehandler
    {
        /**
         * Where to look for modules
         * @var string
         * @access private
         */
        var $modules_dir = './modules/';

         /**
         * Array of modules available. modules/ directory is scanned an stored as
         * array where key=module_name and val is=path/to/module.name.php
         * @var array
         * @access private
         */
        var $available_modules;

          /**
         * Array of disabled modules available. loaded from config file
         * @var array
         * @access private
         */
        var $disabled_modules;

         /**
         * ## Main array of loaded modules Objects
         * @var array
         * @access private
         */
        var $loaded_modules;

        /**
         * Reference to main bot object
         */
        var $_bot;

        /**
         * The configuration vars for module loaded from modules.conf
         */
        var $module_config;

        /**
         * Reference to the bot's server-object
         */
        var $server;

        /**
         * Constructor
         */
        function modulehandler(&$bot)
        {
            $this->_bot = &$bot;
            $this->server = &$bot->server;
        }

        /**
         * Passed a string of space seperated modules, these are exploded, checked and then loaded
         * calls loadModule()
         * @param string $modules_string
         * @return string description of actions taken
         */
        function loadModules($modules_string)
        {
            $modulesToLoad = helpers::toArray($modules_string);
            $return_string = 'Modules loaded: ';
            foreach($modulesToLoad as $moduleName) {
                if(!array_key_exists($moduleName, $this->available_modules)) {
                    $return_string .= $moduleName . ' (doesn\'t exist), ';
                } elseif(array_key_exists($moduleName, $this->available_modules) && isset($this->loaded_modules[$moduleName]) ) {
                    $return_string .= $moduleName . ' (already loaded), ';
                } else {
                    list($loaded, $loadingError) = $this->loadModule($moduleName);
                    if($loaded) {
                        $return_string .= $moduleName . ', ';
                    } else {
                        $return_string .= $moduleName . '(Not Loaded, Error: ' . $loadingError . '), ';
                    }
                }
            }
            $return_string = substr($return_string, 0, -2);
            $this->_bot->logger->addBotLog('## ' . $return_string, BB_LOG_MODULE_ACTION);
            return $return_string;
        }

        /**
         * Loads a named module
         * @param string $module
         * @return array
         */
        function loadModule($module)
        {
            $moduleFile = $this->available_modules[$module];
            include_once($moduleFile);

            if(!class_exists($module)) {
                return array(false, 'File and class name don\'t match.');
            }
            $this->loaded_modules[$module] = new $module();
            list($validated, $missingVars) = $this->validateModule($this->loaded_modules[$module]);
            if(!$validated) {
                return array(false, 'Module missing critical variable(s): ' . $missingVars);
            }

            // Load the config for module
            $config_file = "configs/modules/$module.ini";
            if(file_exists($config_file)){
                $mod_config = helpers::get_ini_vars($config_file);
            }else{
                $mod_config = array();
            }

            // Initialise and start the module
            $this->loaded_modules[$module]->__initialise($this->_bot, $mod_config);
            if(method_exists($this->loaded_modules[$module], 'init')){
                $this->loaded_modules[$module]->init();
            }

            if(isset($this->loaded_modules[$module]->unloadable)) {
                if($this->loaded_modules[$module]->unloadable !== false) {
                    $this->loaded_modules[$module]->unloadable = true;
                }
            } else {
                $this->loaded_modules[$module]->unloadable = true;
            }

            if(!isset($this->loaded_modules[$module]->enabled)) {
                $this->loaded_modules[$module]->enabled = true;
            }
            if($this->loaded_modules[$module]->enabled !== false) {
                $this->loaded_modules[$module]->enabled = true;
            } else {
                unset($this->loaded_modules[$module]);
                return array(false, 'Loading error');
            }
            return array(true, '');
        }


        /**
         * Initialze module(s) - this is called at start up and parses the .config variables
         * @return string List of modules loaded and not loaded
         */
        function initialize()
        {
            $return = array('modules_loaded' => false, 'modules_not_loaded' => false);
            $modulesLoaded = '';
            $modulesNotLoaded = '';
            $this->loadAvailableModuleFiles();

            // Filters the configuration variables/file value
            if($this->_bot->config['modules']) {
                $modules = array();
                $modulesToLoad = helpers::toArray( $this->_bot->config['modules'] );
                if($modulesToLoad[0] == 'all') { // all mods removing ones starting with -
                    $modules = $this->available_modules;
                    // check there's more than all ie - as disabled
                    if(count($modulesToLoad) > 0) {
                        foreach($modulesToLoad as $moduleName) {
                            if($moduleName{0} == '-') {
                                if(array_key_exists(substr($moduleName, 1), $modules)) {
                                    $modulesNotLoaded .= substr($moduleName, 1) . ' (skipped), ';
                                } else {
                                    $modulesNotLoaded .= $moduleName . ' (not exist), ';
                                }
                            }
                        }
                    }
                } else {
                    foreach($modulesToLoad as $moduleName) {
                        if(array_key_exists($moduleName,$this->available_modules)) {
                            $modules[$moduleName] = $this->available_modules[$moduleName];
                        } else {
                            $modulesNotLoaded .= $moduleName . ' (no file), ';
                        }
                    }
                }
                ksort($modules);

                // Load the modules
                foreach(array_keys($modules) as $moduleName) {
                    list($loaded, $loadingError) = $this->loadModule($moduleName);
                    if($loaded) {
                        $modulesLoaded .= $moduleName . ', ';
                    } else {
                        $modulesNotLoaded .= $moduleName . ' (' . $loadingError . '), ';
                    }

                }
                if($modulesLoaded) {
                    $modulesLoaded = substr($modulesLoaded, 0, -2);
                    $this->_bot->logger->addBotLog('## Modules loaded: ' .$modulesLoaded. '.', BB_LOG_MODULE_ACTION, COLOR_GREEN);
                }
                if($modulesNotLoaded) {
                    $modulesNotLoaded = substr($modulesNotLoaded, 0, -2);
                    $this->_bot->logger->addBotLog('!! Modules NOT loaded: ' .$modulesNotLoaded. '.',BB_LOG_MODULE_ACTION, COLOR_RED);
                }
            }
            return $return;
        }

        /**
         * Lists all modules loaded in "memory"
         * @return string the list
         */
        function listLoadedModules()
        {
            $return_string = 'Loaded modules: ';
            if(count($this->loaded_modules > 0)) {
                foreach($this->loaded_modules as $mod => $val) {
                    $return_string .= $mod.', ';
                }
                $return_string = substr($return_string, 0, strlen($return_string)-2);
            }
            return $return_string;
        }

        /**
         * Lists all available modules on disk
         * @return string the list
         */
        function listAvailableModules()
        {
            $this->loadAvailableModuleFiles(); // refresh the file list (we dont want to stop bot:-)
            $return_string = 'Available modules: ';
            foreach($this->available_modules as $mod => $foo) {
                $return_string .= $mod.', ';
            }
            $return_string = substr($return_string, 0, strlen($return_string)-2);
            return $return_string;
        }

        /**
         * Lists all modules not loaded
         * @return string the list
         */
        function listModulesNotLoaded()
        {
            $return_string = 'Modules not loaded: ';
            foreach($this->available_modules as $k => $mod) {
                if( !array_key_exists($k, $this->loaded_modules) ) {
                    $return_string .= "$k, ";
                }
            }
            $return_string = substr($return_string, 0, strlen($return_string)-2);
            return $return_string;
        }

        /**
         * Unloades all modules in string, string is exploded and internal unloadModule() called
         * @param string $modules_string String containing space separated list of modulenames
         * @return string description of actions taken
         */
        function unloadModules($modules_string)
        {
            $modules = helpers::toArray($modules_string);
            $return_string = '';
            foreach($modules as $module) {
                list($success, $str ) = $this->unloadModule($module);
                $return_string .= $str;
            }
            $return_string = trim($return_string);
            $return_string = 'Modules unloaded: '.substr($return_string, 0, strlen($return_string)-1);
            $this->_bot->logger->addBotLog('## ' .$return_string, BB_LOG_MODULE_ACTION);
            return $return_string;
        }


        /**
         * Unload module - internal function that removes a module
         * @param string $modules String containing space separated list of modulenames
         * @return string description of actions taken
         */
        function unloadModule($module)
        {
            if(!array_key_exists($module, $this->available_modules)) {
                $str = $module . ' (doesn\'t exist), ';
                $success = false;
            }elseif(array_key_exists($module, $this->available_modules) && !isset($this->loaded_modules[$module])){
               $str = $module . ' (wasn\'t loaded), ';
               $success = false;
            }elseif($this->loaded_modules[$module]->unloadable == false) {
                $str = $module . ' (can\'t Locked), ';
                $success = false;
            } else {
                unset($this->loaded_modules[$module]);
                $str = $module.' (unloaded), ';
                $success = true;
            }
            return array($success, $str);
        }

        /**
         * Check modules for actions on input
         * @param  buffer      $buffer
         * @param  bambotoo    $bot
         * @return string|None a line to log
         */
        function check($buffer)
        {
            $return = '';
            if($buffer->command == CTCP_VERSION) {
                $this->_bot->sendVersionReply($buffer->channels[0]);
            }

            if(strpos($buffer->text, ' ') !== false) {
                list($trigger, $args)  = explode(' ', $buffer->text, 2);
            } else {
                $trigger = $buffer->text;
                $args = '';
            }
            // detect trigger
            if(substr($trigger, 0, 1) == USER_CMD_CHAR) {
                $exec = USER_MODE;
                $trigger = substr($trigger, 1);
            } elseif(substr($trigger, 0, 1) == ADMIN_CMD_CHAR) {
                if($this->isAdmin($buffer->userhost)) {
                    $exec = ADMIN_MODE;
                    $trigger = substr($trigger, 1);
                } else {
                    $exec = false;
                }
            } else {
                $exec = 'nocmdchar';
            }
            if($exec != false) {
                foreach($this->loaded_modules as $key => $module) {
                    if($module->command == $buffer->command || $module->command == ALL) {
                        if(($exec == $module->mode || $exec == 'nocmdchar') && (isset($module->enabled) && $module->enabled == true)) {
                            if($module->trigger == $trigger && $exec != 'nocmdchar') {
                                //$this->loaded_modules[$key]->callModule($buffer, $this->_bot->info);
                                $this->loaded_modules[$key]->__callModule($buffer);
                                $return = 'Command ' . $module->trigger . ' executed by ' . $buffer->userhost. '.';
                            } elseif($module->trigger == ALL) {
                                $this->loaded_modules[$key]->__callModule($buffer);
                            }
                        }
                    }
                }
                unset($buffer);
                return $return;
            }
            $buffer = null;
            unset($buffer);
        }

        /**
         * Check modules for timebased actions
         */
        function doEvents()
        {
            if(count($this->loaded_modules) > 0) {
                foreach($this->loaded_modules as $key => $module) {
                    $this->loaded_modules[$key]->__doEventModule();
                }
            }
        }

        /**
         * Updates the $available_modules array
         */
        function loadAvailableModuleFiles(){
             $this->disabled_modules = helpers::toArray( $this->_bot->config['disabled_modules']);
             $this->available_modules = array();
              $this->getDirectory($this->modules_dir);
              asort($this->available_modules);
        }

        /**
         * Recusses into modules directories and add files to the $this->available_modules[] array
         * Files and directories starting with _ are ignored, module.php is stripped from name
         * data is stored in $available_modules where key=module_name, val = /path/to/module_file.php
         * @param  string $dir
         */
        function getDirectory($dir)
        {
            if(substr($dir,-1) != '/')
                $dir .= '/';
            $dirhandle = opendir($dir);
            while($file = readdir($dirhandle)) {
                if($file != '.' && $file != '..' && is_dir($dir.$file)) {
                    if(substr($file, 0, 1) != '_')
                        $this->getDirectory($dir.$file);
                } elseif(substr($file, -10) == 'module.php' && substr($file, 0, 1) != '_') {
                    if(!in_array(substr($file, 0, -11),$this->disabled_modules)){
                        $this->available_modules[substr($file, 0, -11)] = $dir.$file;
                    }
                }
            }
        }
        /**
         * Validate a module
         * @param object $moduleObj
         * @return boolean
         */
        function validateModule($moduleObj)
        {
            $neededVars = array('version', 'help', 'usage', 'trigger');
            $missingVars = '';
            foreach($neededVars as $neededVar) {
                if(!isset($moduleObj->$neededVar)) {
                    $missingVars .= '$' . $neededVar . ', ';
                }
            }
            if($missingVars) {
                return array(false, substr($missingVars, 0, -2));
            } else {
                return array(true, '');
            }

        }
        /**
         * Check if a string match a modules name
         * @param string $moduleName
         * @return boolean true if module
         */
        function isModule($moduleName)
        {
        	return in_array($moduleName, array_keys($this->loaded_modules));
        }

        /**
         * Check if user with this host is an admin
         * @param  string  $userhost The full IRC hostname
         * @return boolean true if admin
         */
        function isAdmin($user_host)
        {
            return in_array($user_host,$this->server->admins);
        }
    }
?>