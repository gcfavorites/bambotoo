<?php // VERIFIED
    /**
     * Buffer Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Buffer Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
	class buffer
	{
        /**
         * @var string The incomming string
         * @access public
         */
		var $all;

        /**
         * @var string Nickname of sender if any
         * @access public
         */
		var $nick;

        /**
         * @var string Ident of sender if any
         * @access public
         */
		var $identd;

        /**
         * @var string hostname of sender if any
         * @access public
         */
		var $hostname;

        /**
         * @var string userhost of sender if any
         * @access public
         */
		var $userhost;

        /**
         * @var string What kind of IRC command
         * @access public
         */
		var $command;

        /**
         * @var string Which channel(s) are affected
         * @access public
         */
		var $channels;

        /**
         * @var string A stripped down version of userhost, good for identifing users
         * @access public
         */
		var $smallhost;

        /**
         * @var string The text if any
         * @access public
         */
        var $text;

        /**
         * Contructor takes raw a textline from IRC server and formats into a buffer-object
         * @param  string $raw      a raw line of text from the IRC server
         * @param  const  $mode     SEND or RECEIVE constants
         * @param  object $server   Server Object
         * @param  object $channels Channels Object
         * @return object           Buffer Object
         */
		function buffer($raw, $mode, &$server, &$channels)
		{
            $this->all       = trim($raw);
            $raw = helpers::toArray($raw, 4);
            $this->raw = $raw;
			$posExcl         = strpos($raw[0], '!');
			$posAt           = strpos($raw[0], '@');

			$this->nick      = substr($raw[0], 1, $posExcl - 1);
			$this->identd    = substr($raw[0], $posExcl + 1, $posAt - $posExcl - 1);
			$this->hostname  = substr($raw[0], $posAt + 1);
			$this->userhost  = substr($raw[0], 1);
            $this->smallhost = helpers::toSmallhost($this->userhost);
            $this->channels  = array();
            if(substr($raw[0], 0, 1) == ':') {
				$this->command = strval($raw[1]);
            } else {
				$this->command = strval($raw[0]);
            }

			switch ($this->command) {
				case PRIVMSG:
                    if(isset($raw[3])) {
                        $this->text = substr($raw[3], 1);
                    } else {
                        $this->text = '';
                    }
					if($this->text != '' && ord($this->text{0}) == 1) {
                        /*
                         * This is a CTCP message
                         */
                        $this->text = substr($this->text, 1, -1);
                        if($this->text == 'VERSION') {
						  $this->command = CTCP_VERSION;
                        }
                    }
					if($raw[2] == $server->bot_nick_name) {
						$this->channels[] = $this->nick;
                    } else {
						$this->channels[] = $raw[2];
                    }
 					break;

				case NOTICE:
					$this->notice = substr($raw[3],1);
					$this->channels[] = $raw[2];
					break;

				case JOIN:
                    if($mode == SEND) { // Do nothing when we send JOIN, as the server respond with another JOIN
                        break;
                    }
					$this->channels[0] = $raw[2];

                    if(substr($this->channels[0], 0, 1) == ':') {
                        $this->channels[0] = substr($this->channels[0], 1);
                    }
                    $channels->checkAddChannel($this->channels[0], $this->nick);

					if($this->nick == $server->bot_nick_name) {
                        $server->user_host = $this->userhost;
					}

					break;

				case PART:
                    if($mode == SEND) { // Do nothing when we send PART, as the server respond with another PART
                        break;
                    }
                    if(isset($raw[3])) {
                        $this->text = substr($raw[3], 1);
                    } else {
                        $this->text = '';
                    }
                    $this->channels[] = $raw[2];
                    if($this->nick == $server->bot_nick_name) {
                        $channels->destroyChannel($this->channels[0]);
                    } else {
                        $channels->partChannel($this->channels[0], $this->nick);
                    }
					break;

				case MODE:
					$this->mode = $raw[3];
					$this->channels[] = $raw[2];

					$usersIn = helpers::toArray($raw[3]);
					$modeIn = array_shift($usersIn);

                    for($i = 0; $i < strlen($modeIn); $i++) {
						$chr = substr($modeIn, $i, 1);
						if($chr == '+') {
							for($j = $i + 1; $j < strlen($modeIn)  && $chr != '-' ;$j++) {
								$chr = substr($modeIn, $j, 1);
								if($chr == 'v') {
                                    $user = array_shift($usersIn);
                                    $channels->changeMode($this->channels[0], $user, IRC_MODE_VOICE);
                                } elseif($chr == 'o') {
                                    $user = array_shift($usersIn);
                                    $channels->changeMode($this->channels[0], $user, IRC_MODE_OPERATOR);
                                }
							}
						} elseif($chr == '-') {
							for($j = $i + 1; $j < strlen($modeIn)  && $chr != '+' ;$j++) {
								$chr = substr($modeIn, $j, 1);
								if($chr == 'v') {
									$user = array_shift($usersIn);
                                    $channels->changeMode($this->channels[0], $user, IRC_MODE_VOICE);
								} elseif($chr == 'o') {
									$user = array_shift($usersIn);
                                    $channels->changeMode($this->channels[0], $user, IRC_MODE_OPERATOR);
								}
							}
						}

					}
					break;

				case TOPIC:
					$this->topic = substr($raw[3], 1);
					$this->channels[0] = $raw[2];
                    $channels->setTopic($this->channels[0],$this->topic);
					break;

				case KICK:
                    $split = helpers::toArray($raw[3], 2);
					$this->text = substr($split[1], 1);
					$this->kicked = $split[0];
					$this->channels[] = $raw[2];

                    if($this->kicked == $server->bot_nick_name) {
                        $channels->destroyChannel($this->channels[0]);
                    } else {
                        $channels->quitNick($this->kicked);
                    }
					break;

				case NICK:
                    if($mode == SEND) { // Do nothing when we send NICK, as the server respond with another NICK
                        break;
                    }
                    if(isset($raw[2])) {
                        $this->newnick = substr($raw[2], 1);
                    } else {
                        $this->newnick = '';
                    }
                    foreach($channels->channels as $channelName => $channel) {
                        if($channel->hasNick($this->nick)) {
                            $this->channels[] = $channelName;
                        }
                    }
					if($this->nick == $server->bot_nick_name) {
	                    $server->bot_nick_name = $this->newnick;
						$server->identd        = $this->identd;
						$server->hostname      = $this->hostname;
						$server->user_host      = $this->newnick . '!' . $this->identd . '@' . $this->hostname;
					}
                    $channels->changeNick($this->nick, $this->newnick);
					break;

				case QUIT:
                    if(isset($raw[3])) {
                        $quitmsg = $raw[3];
                    } else {
                        $quitmsg = '';
                    }
                    foreach($channels->channels as $channelName => $channel) {
                        if($channel->hasNick($this->nick)) {
                            $this->channels[] = $channelName;
                        }
                    }
                    $this->text = substr($raw[2],1) . ' ' . $quitmsg;
                    
                    $channels->quitNick($this->nick);
					break;

				// Connected, yay!
				case 001:
					break;

				// Server Info
				case 005:
					$settings = helpers::toArray($raw[3]);
					foreach($settings as $setting) {
						if(strpos($setting, '=') !== false) {
							list($var, $value) = explode('=', $setting);
                        } else {
							$var = $setting;
							$value = false;
						}
                        if($var == 'MAXNICKLEN') {
                            $var = 'NICKLEN';
                        }
						if($var) {
                            $server->settings[$var] = $value;
                        }
					}
					break;

				// Topic on join
				case 332:
					$topic = helpers::toArray($raw[3], 2);
					$this->topic = substr($topic[1], 1);
					$this->channels[] = $topic[0];
                    $channels->setTopic($this->channels[0],$this->topic);
					break;

				// Namelist on join
				case 353:
                    $usersIn = helpers::toArray($raw[3]);
					array_shift($usersIn);
					$chan = array_shift($usersIn);
					$usersIn[0] = substr($usersIn[0], 1);
					foreach($usersIn as $user) {
						if(substr($user, 0, 1) == '@') {
							$users[substr($user, 1)] = IRC_MODE_OPERATOR;
                        } elseif(substr($user, 0, 1) == '+') {
							$users[substr($user, 1)] = IRC_MODE_VOICE;
                        } else {
							$users[$user] = IRC_MODE_NORMAL;
                        }
					}
                    $channels->addNicks($chan, $users);
					break;

				// Nickname already in use
				case 433:
					break;

				// Nickchange too fast
				case 438:
					break;

                //
                case 505:
                    break;
			}
            /*
             * trim_array() is defined in helpers
             */
            $this->channels = trim_array($this->channels);
		}

	}
?>