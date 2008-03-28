<?php

    /**
     * freenode
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

    class freenode extends module
    {
        var $version = '0.1';
        var $mode    = ADMIN_MODE;

        var $trigger = 'freenode';
        var $help    = 'Register with freenode.net\'s nickserv-service';
        var $usage   = '.freenode';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

        var $_password;
        
        function init()
        {
            if(isset($this->config) && $this->config['freenode_ircpass'] != '') {
        	   $this->_password = $this->config['freenode_ircpass'];
            } else {
               $this->enabled = false;
               $this->addBotLog('!! [freenode] module not loaded: No "freenode_ircpass" set in config/modules/freenode.ini', BB_LOG_ERROR, COLOR_RED);
            
            }
        }
        
        function call()
        {
            $this->sendTo('IDENTIFY ' . $this->_password, 'NICKSERV');
            $this->send('Sent "IDENTIFY **********" to "NICKSERV"');    
        }
    }
?>