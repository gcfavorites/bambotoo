<?php // VERIFIED
    /**
     * Constants
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */

    /**
     * Application Name
     */
    define('APP_NAME', 'Bambotoo');

     /**
     * Application Version
     */
     define('APP_VERSION', '1.0.1');

    /**
     * Module constants
     */
    define('USER_MODE', 'user');
    define('ADMIN_MODE','admin');

    /**
     * Command characters
     */
    define('USER_CMD_CHAR', '!');
    define('ADMIN_CMD_CHAR','.');

    /**
     * Send and receive
     */
    define('SEND',    'SEND');
    define('RECEIVE', 'RECEIVE');

    /**
     * IRC constants
     */
    define('PRIVMSG', 'PRIVMSG');
    define('ACTION',  'ACTION');
    define('NOTICE',  'NOTICE');
    define('JOIN',    'JOIN');
    define('PART',    'PART');
    define('MODE',    'MODE');
    define('TOPIC',   'TOPIC');
    define('KICK',    'KICK');
    define('NICK',    'NICK');
    define('QUIT',    'QUIT');
    define('ALL',     '*');

    define('IRC_MODE_NORMAL',   101);
    define('IRC_MODE_VOICE',    102);
    define('IRC_MODE_OPERATOR', 103);

    /**
     * IRC Commands - trailing space on most
     */
    define('CMD_JOIN',    'JOIN ');
    define('CMD_NICK',    'NICK ');
    define('CMD_USER',    'USER ');
    define('CMD_NOTICE',  'NOTICE ');
    define('CMD_PRIVMSG', 'PRIVMSG ');
    define('CTCP_VERSION','CTCP_VERSION');

    /**
     * Logging
     */
    define('BB_LOG_NONE',           0); // Never log these.
    define('BB_LOG_CONNECTION',     1); // Connections and connection errors.
    define('BB_LOG_MODULE_ACTION',  2); // Loading and unloading of modules.
    define('BB_LOG_MODULE_CALL',    3); // Modules called
    define('BB_LOG_CHANNEL',        4); // Bot's joins and parts
    define('BB_LOG_NOTICE',         5); // Notices
    define('BB_LOG_MODULE',         6); // Modules
    define('BB_LOG_ERROR',          9); // Errors
    
    /**
     * Date constants
     */
    define('BAMBOTOO_SECS_IN_MINUTE',60); // seconds in a minute
    define('BAMBOTOO_SECS_IN_HOUR',3600); // seconds in a minute 60 * 60
    define('BAMBOTOO_SECS_IN_DAY',86400); // seconds in a day 60 * 60 * 24
    
    /**
     * Coloured output
     */
    define('COLOR_BOLD',                1);
    define('COLOR_UNDERLINE',           4);
    define('COLOR_HIGHLIGHT',           7);
    define('COLOR_RED',                 31);
    define('COLOR_GREEN',               32);
    define('COLOR_YELLOW',              33);
    define('COLOR_BLUE',                34);
    define('COLOR_PURPLE',              35);
    define('COLOR_LIGHT_BLUE',          36);
    define('COLOR_PURE_WHITE',          37);
    define('COLOR_WHITE_ON_RED',        41);
    define('COLOR_WHITE_ON_GREEN',      42);
    define('COLOR_WHITE_ON_YELLOW',     43);
    define('COLOR_WHITE_ON_BLUE',       44);
    define('COLOR_WHITE_ON_PURPLE',     45);
    define('COLOR_WHITE_ON_LIGHT_BLUE', 46);
    
?>
