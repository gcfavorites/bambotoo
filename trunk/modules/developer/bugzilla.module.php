<?php
    /**
     * bugzilla
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Bugzilla Module - Links to bambotoo's bugzilla.
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     */
    class bugzilla extends module
    {
        var $version = '0.1';
        var $trigger = 'bug';
        var $help    = 'Links to bugzilla.';
        var $usage   = '!bug <id>';
        var $credits = 'Created by Peter Morgan and Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

        function call()
        {
            $bugzilla_url = $this->config['url'];
            if(strpos($this->buffer->text, ' ') !== false) {
                $args = explode(' ', $this->buffer->text, 3);
                if(is_numeric($args[1])){
                    $foo = 'Bug #' .$args[1] . ' ' . $bugzilla_url . 'show_bug.cgi?id='.$args[1];
                }else{
                    $foo = '!bug <id> - Needs to be a number';
                }
            } else {
                $foo = $this->getUsage() . ' -  Bugzilla is at ' . $bugzilla_url;
            }
            $this->send($foo);
        }
    }

?>
