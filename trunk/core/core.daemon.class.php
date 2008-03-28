<?php // VERIFIED
    /**
     * Handles daemonizing
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */

    /**
     * Daemon Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class daemon
    {
        /**
         * @var string
         * @access private
         */
        var $_error;
        /**
         * Constructor, unused
         */
        function daemon()
        {
        }
        
        /**
         * Make a daemon
         * @access public
         * @return boolean true if successful, othervise false
         */
        function daemonize()
        {
            if(!is_callable('pcntl_fork')) {
                $this->_error = 'PHP Process Control Functions missing (Are you on windows?).';
                return false;
            }
            if(!is_callable('posix_setsid')) {
                $this->_error = 'PHP POSIX Functions missing (Are you on windows?).';
                return false;
            }
            
            $pid = pcntl_fork();
            if($pid == -1) {
                $this->_error = 'Could not fork.';
                return false;
            } elseif($pid) {
                 exit(); // we are the parent
            }
            echo APP_NAME . ' continues to run in background.';
            if(!posix_setsid()) {
                $this->_error = 'Could not detach from terminal';
                return false;
            }
            
            // TODO: signal handling (v1.1)
            /*pcntl_signal(SIGTERM, "signalHandler");
            pcntl_signal(SIGKILL, "signalHandler");
            pcntl_signal(SIGHUP,  "signalHandler");*/
            return true;
        }
        
        /*function signalHandler($signalNumber)
        {
            
        }*/
        
        /**
         * Return an error-string, if any
         * @return string The error
         */
        function getError() {
        	return $this->_error;
        }
    }