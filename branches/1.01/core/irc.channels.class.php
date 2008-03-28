<?php // VERIFIED
    /**
     * IRC Channels Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */

    /**
     * IRC Channels
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class irc_channels {
    
        /**
         * Contains an array of irc_channel objects
         * @var array
         */
        var $channels = array();
    
        /**
         * Constructor (unused)
         */
        function irc_channels()
        {
        }
    
        /**
         * Checks if a channels and Nick exists, if not it creates irc_channel object and nick
         * @param string $chan The channel to check
         * @param mixed  $nick The Nickname to check (calls $irc_channel->partNick() )
         */
        function checkAddChannel($channelName, $nickName = null)
        {
            if(!isset($this->channels[$channelName])) {
                $this->channels[$channelName] = new irc_channel($channelName);
            }
            if(isset($nickName)) {
                $this->channels[$channelName]->checkNick($nickName);
            }
        }
        /**
         * Destroy a channel
         * @param string $channelName channel to destroy
         */
        function destroyChannel($channelName)
        {
            if(isset($this->channels[$channelName])) {
                unset($this->channels[$channelName]);
            } else {
                helpers::bugreport(__FILE__, __LINE__, 'Tried to destroy non-existent channel.');
            }
        }
        /**
         * Removes a departing user
         * @param string $channelName The channel to remove nick
         * @param string $nickName    The nick to depart
         */
        function partChannel($channelName, $nickName) {
            if($this->hasChannel($channelName)) {
                $this->channels[$channelName]->partNick($nickName);
            } else {
                helpers::bugreport(__FILE__,__LINE__, 'Tried to part a user from non-existent channel.');
            }
        }
    
        /**
         * Removes a quitting user
         * @param string $nickName
         */
        function quitNick($nickName){
            foreach(array_keys($this->channels) as $channelName) {
                if($this->channels[$channelName]->hasNick($nickName)) {
                    $this->channels[$channelName]->quitNick($nickName);
                }
            }
        }
    
        /**
         * Adds a list of nicks to a channel.
         * @param string $channelName
         * @param array  $nickNames
         */
        function addNicks($channelName, $nickNames){
            if($this->hasChannel($channelName)) {
                $this->channels[$channelName]->nickNames = $nickNames;
            } else {
                helpers::bugreport(__FILE__,__LINE__, 'Tried to add a user to non-existent channel.');
            }
        }
    
        /**
         * Set the toic for the channel
         * @param string $channelName
         * @param string $topic
         */
        function setTopic($channelName, $topic){
            if($this->hasChannel($channelName)) {
                $this->channels[$channelName]->topic = $topic;
            } else {
                helpers::bugreport(__FILE__,__LINE__, 'Tried to change topic in non-existent channel.');
            }
        }
    
        /**
         * Returns a formatted string eg #chan_name[3](foo,bar,users)
         * where [3] are users in channel and (foo,bar,users) are added with the $list_nicks flaf
         * @param array $channels Array of channels to return info on
         * @param boolean $list_nicks Whether to list the chaneels nicknames also
         * @return string formatted string
         */
        function getChannelsAsString($channels, $list_nicks = false){
            $arr = array();
            asort($this->channels);
            $nicks_string = '';
            foreach($this->channels as $chan_ki => $chan_obj){
                if(in_array($chan_ki,$channels)){
                if($list_nicks){
                    $nicks_string = '('.implode(',',array_keys($chan_obj->nickNames)).')';
                }
                $arr[] = $chan_ki.'['.count($chan_obj->nickNames).']'.$nicks_string;
                }
            }
            return implode(', ',$arr);
        }
    
         /**
         * Returns nicknames in all channels eg user, name, foo ,bar
         * @access public
         * @return string All known nicknames seperated by ', '
         */
        function getNicksAsString(){
            $nickNames = getNicksAsArray();
            $nickNamesReturn = array_keys($nickNames);
            asort($nickNamesReturn);
            return implode(', ', $nickNamesReturn);
        }
        /**
         * getNicksAsArray
         * @return array All known nicknames (keys) and their modes (values)
         * @access public
         */
        function getNicksAsArray()
        {
            $nickNames = array();
            foreach($this->channels as $channel) {
                foreach($channel->nickNames as $nickName => $mode){
                    $nickNames[$nickName][$channel] = $mode;
                }
            }
            return $nickNames;
        }
        /**
         * Returns a list of channels names as an array
         * @return array channels names
         */
        function getChannelsAsArray()
        {
            return array_keys($this->channels);
        }
    
        /**
         *  Changes the nicknames across all channels
         * @param string $old_nick The old nickname to replace
         * @param string $new_nick The new nickname to set currently
         */
        function changeNick($oldNick, $newNick){
            foreach(array_keys($this->channels) as $channel) {
                if($this->channels[$channel]->hasNick($oldNick)) {
                    $this->channels[$channel]->changeNick($oldNick, $newNick);
                }
            }
        }
        /**
         * Check if channel exist
         * @param string $channelName
         * @return boolean
         */
        function hasChannel($channelName)
        {
        	return isset($this->channels[$channelName]);
        }
        /**
         * Change mode on a user
         * @param string $channelName
         * @param string $nickName
         * @param string $newMode
         */
        function changeMode($channelName, $nickName, $newMode)
        {
            if($this->hasChannel($channelName)) {
                $this->channels[$channelName]->changeMode($nickName, $newMode);
            } else {
                helpers::bugreport(__FILE__, __LINE__, 'Tried to change mode on user non-existent in channel.');
            }
        }
    }
?>