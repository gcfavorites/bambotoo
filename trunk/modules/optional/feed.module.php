<?php
    /**
     * feed
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */

    /**
     * Feed Module
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     */

    class feed extends module
    {
        var $version = '0.3';
        var $trigger = 'feed';
        var $event   = true;
        var $help    = 'Show news from rss and/or atom feeds in channel automatically. Currently only supporting rss-2.0 feeds';
        var $usage   = '!feed add|show|del [<feedname>]';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';


        /**
         * @var int unix timestamp when to do the next check
         * @access private
         */
        var $_nextCheck;

        /**
         * @var array
         * @access private
         */
        var $_feeds = array();
        
        /**
         * @var string add-help
         */
        var $_addUsage = '!feed add <feedname> <feedURL>';
        
        function init()
        {
            $this->_nextCheck = time() + 10;
            if(ini_get('allow_url_fopen' != 'On')) {
                $this->enabled = false;
                $this->addBotLog('!! [feed] module not loaded: You need to set "allow_url_fopen" to "On" in your php.ini', BB_LOG_ERROR, COLOR_RED);
            }
        }

        function call()
        {
            if(count($this->args) > 2) {
                array_shift($this->args);
                $cmd = array_shift($this->args);
                switch($cmd)
                {
                    case 'add':
                        if(count($this->args) == 2) {
                            $feedName = trim($this->args[0]);
                            $feedUrl  = trim($this->args[1]);
                            $this->addFeed($this->buffer->channels[0], $feedName, $feedUrl);
                        } else {
                            $this->sendAddUsage();
                        }
                        break;

                    case 'del':
                        $this->deleteFeed($this->args[0]);
                        break;

                    case 'show':
                        $this->showFeed($this->args[0]);
                        break;

                    default:
                        $this->sendUsage();
                        break;
                }
            } else {
                if(count($this->_feeds)) {
                    $this->send($this->getUsage() . ' Avalible feeds: ' . join(array_keys($this->_feeds), ', '));
                } else {
                    $this->sendUsage();
                }
            }
        }

        function event()
        {
            if($this->_nextCheck < time()) {
                if(count($this->_feeds) > 0) {
                	foreach($this->_feeds as $feedString => $feedObj) {
                        $feed = &$this->_feeds[$feedString];
                        if($feed->readyToUpdate()) {
                	        if($feed->fetchSource()) {
                                $feed->parseSource();
                                $this->addBotLog('## [feed] Fetched and Parsed the "' . $feedString . '"-feed.');
                                $feed->isChecked();
                            } else {
                                $this->addBotLog('!! [feed] (' . $feed->fetchErrorCount . ') Could not fetch the "' . $feedString . '"-feed.');
                                if($feed->gotFetchError()) {
                                    $feed->_nextCheck = time() + (60 * 60 * 2);
                                    $this->addBotLog('!! [feed] Temporary disabling the "' . $feedString . '"-feed. (2 hours)');
                                }
                            }
                            $new = $feed->getNewItems();
                            if($new) {
                                foreach($new as $item) {
                                    $this->sendTo($item, $feed->getChannel());
                                }
                                $feed->isPrinted();
                            }
                	    }
                	}
                }
                $this->_nextCheck = time() + 10;
            }
        }
        /**
         * Add a feed
         * 
         * @param string $channel
         * @param string $name
         * @param string $url
         */
        function addFeed($channel, $name, $url)
        {
            if(!$this->isFeed($name)) {
                $this->_feeds[$name] = new feeditem($channel, $name, $url);
                if($this->_feeds[$name]->creationError) {
                    $this->send('Error creating the "' . $name . '"-feed. ' . $this->_feeds[$name]->creationError);
                    unset($this->_feeds[$name]);
                } else {
                    $this->send('Added the "' . $name . '"-feed.');
                    $this->addBotLog('## [feed] Fetched and Parsed the "' . $name . '"-feed.');
                }
            } else {
                $this->send('Can\'t add the "' . $name . '"-feed, as already exist. remove it first.');
            }
        }
        /**
         * Delete a feed
         * 
         * @param string $name
         */
        function deleteFeed($name)
        {
            if($this->isFeed($name)) {
                unset($this->_feeds[$name]);
            } else {
                $this->send('Can\'t remove the "' . $name . '"-feed, as it don\'t exist.');
            }
        }
        
        /**
         * Show a feed
         * 
         * @param string $name
         */
        function showFeed($name)
        {
            if($this->isFeed($name)) {
                $latest = $this->_feeds[$name]->getLatestNews();
                if($latest) {
                    $this->send($latest);
                } else {
                    $this->send('Couldn\'t get latest news from the "' . $name . '"-feed. It\'s probably not updated yet.');
                }
            } else {
                $this->send('Can\'t show the "' . $name . '"-feed, as it don\'t exist.');
            }
        }
        
        /**
         * Check if a feed exist
         * 
         * @param string $name
         * @return boolean
         */
        function isFeed($name)
        {
            return array_key_exists($name, $this->_feeds);
        }
        /**
         * Send add-help
         */
        function sendAddUsage()
        {
            $this->send($this->_addUsage);
        }
    }
    /**
     * Feeditem Class
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     */
    class feeditem
    {
        /**
         * @var boolean
         */
        var $creationError = false;
        
        /**
         * @var string
         */
        var $channel;
        
        /**
         * @var string
         */
        var $name;
        
        /**
         * @var string
         */
        var $url;
        
        /**
         * @var int
         */
        var $_nextCheck;
        
        /**
         * @var object
         */
        var $_urlfetcher;
        
        /**
         * @var array this feeds news
         */
        var $_news = array();
        
        /**
         * @var object
         */
        var $_latestNews;
        
        /**
         * @var string
         */
        var $_source;
        
        /**
         * @var int
         */
        var $fetchErrorCount = 0;
        
        /**
         * @var object
         */
        var $_parser;
        
        /**
         * @var string
         */
        var $_error;

        /**
         * Constructor
         * 
         * @param string $channel
         * @param string $name
         * @param string $url
         */
        function feeditem($channel, $name, $url)
        {
            $this->channel = $channel;
            $this->name = $name;
            $this->url = $url;
            $this->_nextCheck = time() + 15;
            $this->_parser = new xmlparser();
            $this->_urlfetcher = new urlfetcher($this->url);
            if($this->fetchSource()) {
                if(!$this->parseSource()) {
                    $this->creationError = 'Couldn\'t parse the fetched feed\'s source. Parser error: ' . $this->getError();
                } else {
                    $this->isChecked();
                    $this->isPrinted();
                }
                
            } else {
                $this->creationError = 'Couldn\'t fetch the feed\'s source. Fetch error: ' . $this->_urlfetcher->getError();
            }
        }
        /**
         * @return string
         */
        function getChannel()
        {
            return $this->channel;
        }

        /**
         * @return string
         */
        function getError()
        {
            return $this->_error;
        }
        
        /**
         * @return boolean
         */
        function readyToUpdate()
        {
            return ($this->_nextCheck < time());
        }
        
        /**
         * Mark that this feed been checked
         */
        function isChecked()
        {
            $this->_nextCheck = time() + (60 * 16);
        }

        /**
         * Mark that this feed been printed
         */
        function isPrinted()
        {
        	$this->_latestNews = $this->_news[0];
        }
        
        /**
         * @return string
         */
        function getLatestNews()
        {
            if(count($this->_news)) {
                $this->isPrinted();
                return $this->formatNews($this->_news[0]);
            } else {
                return false;
            }
        }
        
        /**
         * @return array
         */
        function getNewItems()
        {
            $newNews = array();
            foreach($this->_news as $news) {
                if($news->title == $this->_latestNews->title) {
                    break;
                }
                $newNews[] = $this->formatNews($news);
            }
            if(count($newNews)) {
                $this->isPrinted();
                return array_reverse($newNews);
            } else {
                return false;
            }
        }
        
        /**
         * @return string
         */
        function formatNews($news) {
            return 'Feed ' . $this->name .': ' . $news->title . ' ' . $news->link;
        }
        
        /**
         * Mark that a error fetching occured
         */
        function gotFetchError()
        {
            $this->fetchErrorCount++;
            if($this->fetchErrorCount > 5) {
                return false;
            }
        }

        /**
         * @return boolean
         */
        function fetchSource()
        {
            $this->_source = $this->_urlfetcher->getContent(true, true);
            if($this->_source) {
                return true;
            } else {
                return false;
            }
        }
        
        /**
         * @return string
         */
        function parseSource()
        {
            $input = $this->_parser->xml2array($this->_source);
            unset($this->_source);
            if($input) {
                if(isset($input['RDF:RDF'][0]['attr']['XMLNS'])) {
                    if(substr($input['RDF:RDF'][0]['attr']['XMLNS'], 0 , 23) == 'http://purl.org/rss/1.0') {
                        $news = $this->parseRSS1($input);
                    }
                } elseif(isset($input['RSS'][0]['attr']['VERSION'])) {
                    $version = $input['RSS'][0]['attr']['VERSION'];
                    if($version == '0.91') {
                        $news = $this->parseRSS091($input);
                    } elseif($version == '2.0') {
                        $news = $this->parseRSS2($input);
                    }
                } elseif(isset($input['FEED'][0]['attr']['XMLNS'])) {
                    if(substr($input['FEED'][0]['attr']['XMLNS'], 0, 27) == 'http://www.w3.org/2005/Atom') {
                        $news = $this->parseAtom($input);
                    }
                }
            } else {
                $this->_error = 'Not an xml-file.';
            }
            if(isset($news) && $news != false) {
                $news        = array_merge($news, $this->_news);
                $this->_news = array_unique($news);
                return true;
            } else {
                return false;
            }
        }
        
        /**
         * @param array $data
         * @return array
         */
        function parseRSS091($data) {
            return $this->parseRSS2($data);
        }
        
        /**
         * @param array $data
         * @return array
         */
        function parseRSS1($data) {
            if(count($data['RDF:RDF'][0]['value']['ITEM']) > 0) {
                $input = &$data['RDF:RDF'][0]['value']['ITEM'];
            } else {
                print_r($data);
                return false;
            }
            $news = array();
            foreach($input as $item) {
                $item = &$item['value'];
                if(isset($item['LINK'][0]['value']) && isset($item['TITLE'][0]['value'])) {
                    $id = count($news);
                    $news[$id] = new news();
                    $news[$id]->link  = $item['LINK'][0]['value'];
                    $news[$id]->title = str_replace("\n", ' ', $item['TITLE'][0]['value']);
                }
            }
            return $news;
        }
        
        /**
         * @param array $data
         * @return array
         */
        function parseRSS2($data) {
            if(count($data['RSS'][0]['value']['CHANNEL'][0]['value']['ITEM']) > 0) {
                $input = &$data['RSS'][0]['value']['CHANNEL'][0]['value']['ITEM'];
            } else {
                return false;
            }
            $news = array();
            foreach($input as $item) {
                $item = &$item['value'];
                if(isset($item['LINK'][0]['value']) && isset($item['TITLE'][0]['value'])) {
                    $id = count($news);
                    $news[$id] = new news();
                    $news[$id]->link  = $item['LINK'][0]['value'];
                    $news[$id]->title = str_replace("\n", ' ', $item['TITLE'][0]['value']);
                }
            }
            return $news;
        }
        
        /**
         * @param array $data
         * @return array
         */
        function parseAtom($data) {
            if(count($data['FEED'][0]['value']['ENTRY'] > 0)) {
                $input = &$data['FEED'][0]['value']['ENTRY'];
                print_r($input);
            } else {
                return false;
            }
            $news = array();
            foreach($input as $item) {
                $item = &$item['value'];
                if(isset($item['LINK'][0]['attr']['HREF']) && isset($item['TITLE'][0]['value'])) {
                    $id = count($news);
                    $news[$id] = new news();
                    $news[$id]->link  = $item['LINK'][0]['attr']['HREF'];
                    $news[$id]->title = str_replace("\n", ' ', $item['TITLE'][0]['value']);
                }
            }
            return $news;
        }
    }

    /**
     * News Class
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     */
    class news
    {
        /**
         * @var string
         */
        var $title;
        
        /**
         * @var string
         */
        var $link;
    }