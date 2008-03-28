<?php // VERIFIED
    /**
     * IRC Channel Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    
    /**
     * IRC Channel
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class irc_channel
    {
    
        /**
         * The channel name (pretty pointless really ?? maybe not)
         */
        var $channel;
    
        /**
         * The topic for this channel
         */
        var $topic;
    
        /**
         * Array that stores the nicknames on this channel
         */
        var $nickNames = array();
    
        /**
         * Timestamp of when this channel was joined
         */
        var $join_time;
    
        /**
         * Class constructor - adds an user and sets start time
         * @param string $channel Channel to create eg #foo
         * @param string $nick Nickname to add to this channel
         */
        function irc_channel($channel, $nickName = null, $mode = IRC_MODE_NORMAL) {
            $this->channel = $channel;
    
            if(!is_null($nickName)){
                $this->nickNames[$nickName] = $mode;
            }
            $this->join_time = time();
        }
    
        /**
         * Called by /JOIN and others - checks if the nick is in the list, if not it adds it
         * @param string $nick - Nickname to add to this channel
         */
        function checkNick($nickName) {
            if(!$this->hasNick($nickName)) {
                $this->nickNames[$nickName] = IRC_MODE_NORMAL;
            } else {
                helpers::bugreport(__FILE__, __LINE__, 'Tried to add an existent nick.');
            }
        }
        
        /**
         * Check if user exist in channel
         * @param string $nick - Nickname to check 
         * @return boolean true if exist
         */
        function hasNick($nickName) {
            return array_key_exists($nickName, $this->nickNames);
        }
        
        /**
         * Called by /PART - removes a nickname from this channel
         * @param string $nick - Nickname to remove from this channel
         */
        function partNick($nickName){
            if($this->hasNick($nickName)) {
                unset($this->nickNames[$nickName]);
            } else {
                helpers::bugreport(__FILE__, __LINE__, 'Tried to part a non-existent user.');
            }
        }
    
        /**
         * Called by /QUIT - removes a nickname from this channel
         * @param string $nick - Nickname to remove from this channel
         */
        function quitNick($nickName)
        {
            if($this->hasNick($nickName)) {
                unset($this->nickNames[$nickName]);
            } else {
                helpers::bugreport(__FILE__, __LINE__, 'Tried to quit a non-existent user.');
            }
        }
    
        /**
         * Changes the nickname of an user, preserving the $mode
         * @param string $oldNick The old nickname to abandon
         * @param string $newNick The new nickname to set
         */
        function changeNick($oldNick, $newNick)
        {
            if($this->hasNick($oldNick)) {
                $mode = $this->nickNames[$oldNick];
                unset($this->nickNames[$oldNick]);
                $this->nickNames[$newNick] = $mode;
            } else {
                helpers::bugreport(__FILE__, __LINE__, 'Tried to change nick on non-existent user.');
            }
        }
        /**
         * Change mode of a user
         * @param string $nickName
         * @param int    $newMode
         */
        function changeMode($nickName, $newMode)
        {
            if($this->hasNick($nickName)) {
                $this->nickNames[$nickName] = $newMode;
            } else {
                helpers::bugreport(__FILE__, __LINE__, 'Tried to change mode on non-existent user.');
            }
        }
    }
?>