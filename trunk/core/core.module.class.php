<?php // VERIFIED

    /**
     * Abstract Module class
     * This is the main object that all modules are derived from
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Module Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @abstract
     */
    class module
    {
        /**
         * Hardcoded value of the module version starting at 0.1
         * Required
         * @var string
         * @access public
         */
        var $version;

        /**
         * The authors name, web site etc. This is shown with !about <trigger>
         * Optional
         * @var string
         * @access public
         */
        var $credits;

        /**
         * A text describing the modules purpose. This is shown with !help <trigger>
         * Don't put a dot in the end of this string
         * Required
         * @var string
         * @access public
         */
        var $help;

        /**
         * A text description of the module, bit more detail, This is shown with !about -d <trigger>
         * Optional
         * @var string
         * @access public
         */
         var $description;

         /**
          * The usage syntax of the bot. ie  list of command etc. This is appended to !help <trigger>
          * Ex: !mymodule <required argument> [<optional argument>]
          * Required
          * @var string
          * @access public
          *
          */
        var $usage;

        /**
         * Either USER_MODE or ADMIN_MODE constants
         * If set to ADMIN_MODE, the user executing it needs to be authenticated first (with "!admin <password>")
         * Defaults to USER_MODE
         * @var string
         * @access public
         */
        var $mode = USER_MODE;

        /**
         * Type of IRC-command for calling call() in inherited class
         * it can be one of the constants
         * PRIVMSG, NOTICE, JOIN, PART, MODE, TOPIC, KICK, NICK, QUIT or ALL to match any command.
         * Defaults to PRIVMSG
         * @var string
         * @access public
         */
        var $command = PRIVMSG;

        /**
         * Trigger for firing call() in inherited class
         * It MUST be '*' if $command is anything but PRIVMSG
         * Required
         * @var string
         * @access public
         */
        var $trigger;


        /**
         * Whether this modules is unloadable. Modules unloaded are "removed" from memory
         * Optional
         * @var boolean
         * @access public
         */
        var $unloadable = true;

        /**
         * Whether this modules is enabled or not, if module has unsatisfied dependencies, modules' init()
         * method should set this to false
         * @var boolean
         * @access public
         */
        var $enabled;

         /**
         * The bot referenced (But not allowed to be used in inherited class, just here to use send())
         * @var object
         * @access private
         */
        var $__bot;

        /**
         * The current buffer referenced
         * @var object
         * @access protected
         */
        var $buffer;

        /**
         * object details of the server
         * @var array
         * @access protected
         */
        var $server;

        /**
         * Var channel details object. $users, $topics
         * @var array
         * @access protected
         */
        var $channel;

        /** The configurationon variables for this class, loaded from configs/modules/<module>.ini
         * @var array
         * @access protected
         */
        var $module_config;

        /** This is the exploded $this->buffer->text into an array. The first arg[0] will ALWAYS be
         * the command, like bash
         * @var array
         * @access protected
         */
        var $args;

        /** This is a flag set when the number of arguments is more than one, ie there are arguments/actions to process.
         * This is used in the module as:
         * <code>
         * <?php
         *     if($this->arguments) {
         *         // do something
         *     } else {
         *         $this->send($usage);
         *     }
         * ?>
         * </code>
         * @var int
         * @access protected
         */
        var $arguments;

        /**
         * This is the initializing method called by the moduleHandler class when it loads the module
         * @access public
         * @param object $bot
         * @param array  $module_config
         */
        function __initialise(&$bot, $module_config)
        {
            $this->__bot = &$bot;
            $this->server = &$bot->server;
            $this->channels = &$bot->channels;
            $this->config = $module_config;
            $this->server = &$bot->server;

        }

        /**
         * This is the main method called by the the moduleHandler class on a trigger hit
         * @access public
         * @param object $buffer
         */
        function __callModule($buffer)
        {
            $this->buffer = $buffer;
            $this->args = helpers::toArray($this->buffer->text);
            $this->arguments = count($this->args) > 1;
            $this->call();
            $this->__cleanup();
        }

        /**
         * Cleanup and unset some finished vars
         * @access protected
         */
        function __cleanup()
        {
            $this->buffer = null;
        }

        /**
         * This is the method called as often as possible
         * @access public
         */
        function __doEventModule()
        {
            $this->event();
        }

        /**
         * Send text to current channel/user
         * @access protected
         * @param string $text
         * @param int    $time unix timestamp when to send the data
         */
        function send($text, $time = 0)
        {
            $text = trim($text);
            $target = $this->buffer->channels[0];
            $this->__bot->send(CMD_PRIVMSG . $target . ' :' . $text, $time);
        }

        /**
         * Send text to selected channel/user
         * @access protected
         * @param string $text   what to send
         * @param string $target where to send it
         * @param int    $time   unix timestamp when to send the data
         */
        function sendTo($text, $target, $time = 0)
        {
            $text   = trim($text);
            $target = trim($target);
            if($target != '') {
                $this->__bot->send(CMD_PRIVMSG . $target . ' :' . $text, $time);
            } else {
                helpers::bugreport(__FILE__,__LINE__, 'Tried to send data to non-existent target.');
            }
        }

        /**
         * Send raw text
         * @access protected
         * @param string $text
         * @param int    $time unix timestamp when to send the data
         */
        function sendRaw($text, $time = 0)
        {
            if($text) {
                $this->__bot->send($text, $time);
            } else {
                helpers::bugreport(__FILE__,__LINE__, 'Tried to send empty string.');
            }
        }

        /**
         * Get the usage of this module
         * @return string How to use this module
         * @access public
         */
         function getUsage()
        {
            $returnString = 'Usage: ' . $this->usage;
            return $returnString;
        }

        /**
         * Send the usage to the current channel or user
         * @access public
         */
        function sendUsage()
        {
            $this->send($this->getUsage());
        }

        /**
         * Adds an entry to the BOT log, NOT the channel log - useful for debug messages etc
         * @access protected
         * @param string $logString
         * @param int    $logLevel
         * @param int    $color     see core.constants.php for avalible colors
         */
        function addBotLog($logString, $logLevel = BB_LOG_MODULE, $color = 0)
        {
            $this->__bot->logger->addBotLog($logString, $logLevel, $color);
        }



        /**
         * This is the method called as often as possible
         * @abstract
         * @access protected
         */
        function event()
        {
        }
    }
?>
