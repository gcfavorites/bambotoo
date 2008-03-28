<?php // VERIFIED
    /**
     * Main Bambotoo Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */


    /**
     * Required files and classes grouped by purpose and ambition
     */
    // Core
    require_once('core/core.constants.php');
    require_once('core/core.buffer.class.php');
    require_once('core/core.socket.class.php');
    require_once('core/core.urlfetcher.class.php');

    // IRC
    require_once('core/irc.class.php');
    require_once('core/irc.server.class.php');
    require_once('core/irc.channels.class.php');
    require_once('core/irc.channel.class.php');

    // Logging
    require_once('core/core.logger.class.php');
    require_once('core/core.filehandler.class.php');

    // Modules
    require_once('core/core.modulehandler.class.php');
    require_once('core/core.module.class.php');

    // Functions and utils
    require_once('core/core.helpers.class.php');
    require_once('core/core.xmlparser.class.php');
    require_once('core/core.compatibility.php');

    /**
     * Main bambotoo Class
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class bambotoo extends irc
    {


         /**
         * Configuration file - can be overridden as command line argument
         * @access public
         * @var string
         */
        var $config_file   = 'configs/bambotoo.ini';

         /**
         * Module Configuration file - can be overridden as command line argument
         * @access public
         * @var string
         */
        var $modules_config_dir = 'configs/modules/';

         /**
         * Variable for the config files paramaters loaded from .ini
         * @access public
         * @var array
         */
        var $config = array();

        /**
         * Variable for the module config files paramaters loaded from 'modules.conf'
         * @access public
         * @var array
         */
        var $modules_config = array();

         /**
         * File handler object initialised in _init()
         * @access public
         * @var object
         */
        var $fileHandler;

        /**
         * Modulehandler object
         * @access public
         * @var object
         */
        var $moduleHandler;

         /**
         * Modules to automatically load at startup - loaded from config file
         * @access public
         * @var string
         */
        var $modules = 'authenticate adminhelp insmod quit';

         /**
         * Admin password - loaded from config file
         * @access public
         * @var string
         */
        var $adminpass = '';

        /**
         * Channels to log, loaded to array() in the _init() function
         * @access private
         * @var array
         */
        var $_log_channels;

        /**
         * Leading string that fires an user level module eg !help
         * @access public
         * @var string
         */
        var $usercmdchar = '!';

        /**
         * Leading string that fires an admin module eg .auth
         * @access public
         * @var string
         */
        var $admincmdchar = '.';


        /**
         * Timestamp value used with uptime();
         * @access public
         * @var boolean
         */
        var $start_time;

        /**
         * Whether the bot should quit - This is a flag checked with while(!$quit)
         * @access public
         * @var boolean
         */
        var $quit = false;

        /**
         * Array that 'caches' messages until logger is created
         * @access public
         * @var array
         */
        var $_startup_messages;

        /**
         * @access private
         */
        var $_gotConsole = true;

        /**
         * Constructor
         */
        function bambotoo()
        {
            /*
             * set default configuration variables
             */
            $this->config['nickname']                 = 'Bambotoo';
            $this->config['altnickname']              = 'Bambotoo^';
            $this->config['server']                   = 'localhost';
            $this->config['port']                     = 6667;
            $this->config['channels']                 = '#bambotoo';
            $this->config['modules']                  = 'admin adminhelp insmod quit';
            $this->config['disabled_modules']         = '';
            $this->config['adminpass']                = '';
            $this->config['log_channels']             = '';
            $this->config['log_level']                = '1 2 3 4 5 6 9';
            $this->config['log_dir']                  = 'logs/';
            $this->config['log_filename_date_format'] = "dMY";
            $this->config['log_date_time_format']     = "[H:i]";
            $this->config['console_date_format']      = "H:i:s";

            /*
             * declare some static classes - ie not requiring config (pre config)
             */
            $this->server = new irc_server();
            $this->channels = new irc_channels();
        }

        /**
         * Initializes various variables and checks
         * @access public
         */
        function _init()
        {
            $this->fileHandler = new fileHandler($this->config);
            /*
             * create array of channels to log
             */
            $this->_log_channels = helpers::toArray($this->config['log_channels']);

        }

        /**
         * Sets a class variable's value
         * @param string $var The variable to set
         * @param mixed  $value The value to set
         */
        function setVar($var, $value)
        {
           $this->$var = $value;
        }

        /**
         * returns a class variable's value
         * @param  string $var String containing the variable name to return eg 'appName'
         * @return mixed  The variable value
         */
        function getVar($var) {
            return $this->$var;
        }


        /**
         * Starts Bambotoo
         * @access public
         */
        function run(){

            $this->start_time = time();
            $this->_startup_messages[] =  array('@@ Starting '.APP_NAME . ' ' . APP_VERSION, BB_LOG_NOTICE, COLOR_BOLD);

            /*
             * Load configuration
             */
             $this->_loadConfig();

            /*
             * Initialse some paramaters
             */
            $this->_init();

            /*
             * Create logged and add startup messages
             */
            $this->logger = new logger($this);
            if(!$this->_gotConsole) {
                $this->logger->disableAllConsoleOutput();
            }
            foreach($this->_startup_messages as $message) {
                $this->logger->addBotLog($message[0], $message[1], $message[2]);
            }
            $this->_startup_messages = null;

            $this->logger->printLogging();

            /*
             * Create modulehandler and initialize it
             */
            $this->moduleHandler = new modulehandler($this);
            $this->moduleHandler->initialize();

            // Start Bambotoo application loop
            do {
                list($connected, $status) = $this->connectToServer();
                if($connected) {
                    $this->logger->addBotLog('## ' . $status, BB_LOG_CONNECTION, COLOR_GREEN);
                } else {
                    $this->logger->addBotLog('## ' . $status, BB_LOG_CONNECTION, COLOR_RED);
                }
                // Main application loop
                if($connected) {
                    while($this->server->connected && !$this->quit) {
                        $this->doReceive();
                        $this->doSend();
                        $this->moduleHandler->doEvents($this);
                        usleep(300000);
                    }
                } elseif(!$this->quit) {
                    sleep(10);
                }
            } while(!$this->quit);
            $this->logger->addBotLog('@@ Stopping ' . APP_NAME, BB_LOG_NOTICE, COLOR_BOLD);
            sleep(2);
            $this->socket->disconnect();
        }

        /**
         * checks the config file exists and parses the .ini file
         * @access private
         */
        function _loadConfig()
        {
            // Load Main config
            $file = $this->config_file;
            if(!file_exists($file)) {
                helpers::echoc("Fatal error: the config file '".$file."' does not exists, please check README\n\n", COLOR_RED);
                die(1);
            }

            if(!is_readable($file)) {
                helpers::echoc("Fatal error: the file '".$file."' exists but it not readable, please check README\n\n", COLOR_RED);
                die(1);
            }

            $config_from_ini =  helpers::get_ini_vars($file, false);
            foreach($config_from_ini as $var => $value) {
                $this->config[$var] = $value;
            }

            // set channels to join at startip
            $this->channels_to_autojoin = helpers::toArray($this->config['channels']);

            // Move some vars to $server object
            $this->server->host = $this->config['server'];
            $this->server->port = $this->config['port'];
            $this->server->admin_pass = $this->config['adminpass'];
            $this->server->bot_nick_name = $this->config['nickname'];
            $this->server->bot_alt_nick_name = $this->config['altnickname'];
            //$this->server->user_name = $this->server->bot_nick_name ??? not sure
            $this->_startup_messages[] = array('## '.APP_NAME." config file '{$file}' succesfully loaded", BB_LOG_NOTICE, COLOR_GREEN);

        }

        /**
         * Shows and Outputs the BamBotoo logo
         * @access private
         */
        function showLogo()
        {
            $logo = <<<EOF
 ___                   ___         _
|  _ \                |  _ \      | |
| |_) | __ _ _ __ ___ | |_) | ___ | |_ ___   ___
|  _ < / _` | '_ ` _ \|  _ < / _ \| __/ _ \ / _ \
| |_) | (_| | | | | | | |_) | (_) | || (_) | (_) |
|____/ \__,_|_| |_| |_|____/ \___/ \__\___/ \___/

EOF;
            echo $logo . "\n";
        }

        /**
         * Turn off all console output
         */
        function disableAllConsoleOutput()
        {
            $this->_gotConsole = false;
        }
   }
?>