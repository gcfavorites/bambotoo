<?php
    /**
     * skel
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-misc-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Skel Module -
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-misc-modules
     */

    class skel extends module
    {
        // The access to a module, either ADMIN_MODE or USER_MODE
        var $mode    = USER_MODE;

        // the text that triggers the module
        var $trigger = 'skel';

        var $version = '0.0';
        var $credits = 'your credits go here';

        var $usage   = '!skel';
        var $help    = 'Help about skel here';

        // list your vars here with a leading _ is recommended
        var $_mc2;
        var $_name = 'ae';

        function init(){
            // This is the function called when class is created
            // This is not mandatory and is a replacment for the php5 __construct()
            // and as is extende in php4 also the skel()

        }

        function call()
        {
            // this send a message to the channel
            $this->send('Hi, Im skel');
        }
    }

?>
