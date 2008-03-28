<?php
    /**
     * vote
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Vote Module
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     */
    class vote extends module
    {
        var $version = '0.1';
        var $trigger = 'vote';
        var $help    = 'Start a vote on any subject';
        var $usage   = '!vote start|end|yes|no [question]';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';
        
        /**
         * @var array the current open vote
         */
        var $_vote;
        
        function call()
        {
            if(count($this->args) > 1) {
                array_shift($this->args);
                $cmd = array_shift($this->args);
                switch($cmd) {
                    case 'start':
                        if(isset($this->args[0])) {
                            if(isset($this->_vote[$this->channel])) {
                                $this->send('There already a vote in progress in this channel, question is: ' . $this->_vote[$this->channel]->question . ' Stop the vote with "!vote end"');
                            } else {
                                $this->_vote[$this->channel] = new voteitem(join($this->args, ' '));
                                $this->send('Vote started, question is: ' . $this->_vote[$this->channel]->question . ' Answer by "!vote yes" or "!vote no".');
                            }
                        } else {
                            $this->send('Start a vote with "!vote start <question>?"');
                        }
                        break;
                    case 'stop':
                    case 'end':
                        if(isset($this->_vote[$this->channel])) {
                            $this->send($this->_vote[$this->channel]->result());
                            unset($this->_vote[$this->channel]);
                        } else {
                            $this->send('There is no vote right now. Start one with "!vote start <question>?"');
                        }
                        break;
                    case 'yes':
                        if(isset($this->_vote[$this->channel])) {
                            $this->send($this->_vote[$this->channel]->yes($this->buffer->userhost));
                        } else {
                            $this->send('There is no vote right now. Start one with "!vote start <question>?"');
                        }
                        break;
                    case 'no':
                        if(isset($this->_vote[$this->channel])) {
                            $this->send($this->_vote[$this->channel]->no($this->buffer->userhost));
                        } else {
                            $this->send('There is no vote right now. Start one with "!vote start <question>?"');
                        }
                        break;
                    default:
                        $this->sendUsage();
                        break;
                }
            } elseif(isset($this->_vote[$this->channel])) {
                $this->send('Question is: ' . $this->_vote[$this->channel]->question . ' Answer by "!vote yes" or "!vote no".');
            } else {
                $this->sendUsage();
            }
            print_r($this->_vote);
        }
    }
    class voteitem
    {
        /**
         * @var string The question
         */
        var $question;
        /**
         * @var array The answers
         */
        var $answers;
        
        function voteitem($q)
        {
            if($q[strlen($q) - 1] != '?') {
                $q .= '?';
            }
            $this->question = $q;
            $this->answers = array();
        }
        function result()
        {
            if(count($this->answers) == 0) {
                return 'No one answered the question: ' . $this->question; 
            } else {
                $yes = 0;
                $no  = 0;
                foreach($this->answers as $answer) {
                    if($answer == 'yes') {
                        $yes++;
                    } elseif($answer == 'no') {
                        $no++;
                    }
                }
                $total = $yes + $no;
                if($yes > $no) {
                    $percent = round($yes / $total, 2) * 100 . '%';
                    $vinner = 'Yes (' . $percent . ') Yes: ' . $yes . ', No: ' . $no;
                } elseif($no > $yes) {
                    $percent = round($no / $total, 2) * 100 . '%';
                    $vinner = 'No (' . $percent . ') Yes: ' . $yes . ', No: ' . $no;;
                } else {
                    $vinner = 'It\'s a tie!';
                }
                return 'The question: ' . $this->question . ' Anwser: ' . $vinner;
            } 
        }
        function yes($userhost) {
            if(isset($this->answers[$userhost])) {
                return 'You have already voted on the subject!';
            } else {
                $this->answers[$userhost] = 'yes';
                return 'You vote has been counted.';
            }
        }
        function no($userhost) {
            if(isset($this->answers[$userhost])) {
                return 'You have already voted on the subject!';
            } else {
                $this->answers[$userhost] = 'no';
                return 'You vote has been counted.';
            }
        }
    }
?>