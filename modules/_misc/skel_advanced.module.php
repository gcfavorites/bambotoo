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
     * @subpackage bambotoo-custom-modules
     */
    class skel_advanced extends module
    {
        var $version     = '0.1';
        var $credits     = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo';

        var $trigger     = 'skel_adv';
        var $help        = 'Help about skel here';
        var $description = 'A text description of the module, bit more detail';
        var $usage       = '!skel';

        var $mode        = USER_MODE;
        var $command     = PRIVMSG;
        var $unlodable   = true;
        var $enabled     = true;

        function init()
        {

            $this->localvar = 'This is the place to init/store information.';

            // In this var we can find configuration from skel.ini in config/modules/
            print_r($this->module_config);
        }

        function call()
        {
            // Three ways to send:
            $this->send('Hi, Im skel');
            $this->sendTo('pete', 'hi Pete, Im skel.');
            $this->sendRaw(PRIVMSG . 'pete :hi Pete, Im skel.');

            // Do some logging
            $this->addBotLog('skel was called!');

            // Check arguments
            if($this->arguments) {
                // We have arguments, lets check them out:
                print_r($this->args);
            }
        }

        function event()
        {
            if('something' == 'somthing else') {
                // do something
            }
        }
    }
?>
