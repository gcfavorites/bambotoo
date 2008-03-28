<?php //VERIFIED
    /**
     * IRC Server
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * IRC Server Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class irc_server
    {
        /**
         *  the irc server's adress eg irc.freenode.net
         */
        var $host;
    
        /**
         * irc server's port
         */
        var $port;
    
        /**
         * nickname of the bot
         */
        var $bot_nick_name;
        
        /**
         * alternative nickname of the bot
         */
        var $bot_alt_nick_name;
    
        /**
         * username of the bot
         */
        var $bot_user_name = 'bambotoo';
        
        /**
         * hostname of the bot
         */
        var $user_host;
    
        /**
         *  this is the timestamp of when connected
         */
        var $connect_time;
    
        /**
         * @access public
         * @var boolean Flag to indicate if connected to irc server successfully
         */
        var $connected = false;
        
        /**
         * @access protected
         * @var boolean Flag to indicate if irc-server is ready to recive JOIN
         */
        var $ready_to_join = false;
         
        /**
         * array of Logged in admins for this server
         * note: key is the admin auth
         */
        var $admins = array();
    
        /**
         * Admin password from config_file, auth by !admin module
         */
        var $admin_pass;
    
        /**
         * Server setting loaded from server (if it sends them)
         */
        var $settings;
    
        /**
         * Constructor. This sets the MAXNICKLEN to a default of 8
         */
        function irc_server()
        {
            $this->settings['MAXNICKLEN'] = 8;
        }
    
        /**
         * returns the uptime and start time of server
         * @return string
         */ 
        function getInfo()
        {
            $str = $this->host.':'.$this->port.' ';
            $str .= ' - Connected: '.date('d-m-y H:i:s',$this->connect_time);
            $str .= ' - Uptime: '.helpers::getUptimeString($this->connect_time);
    
            return $str;
        }
    
    }
?>