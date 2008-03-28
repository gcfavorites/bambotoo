<?php

    /**
     * fetches  URL's source
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Urlfetcher Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class urlfetcher
    {
        
        /**
         * @var string
         * @private
         */
        var $_url;
        
        /**
         * @var int
         * @private
         */
        var $_max_bytes;
        
        /**
         * @var int
         * @private
         */
        var $_timeout = 5;
        
        /**
         * @var string
         * @private
         */
        var $_mime;
        
        /**
         * @var string
         * @private
         */
        var $_error;
        
        /**
         * Constructor
         * @param string $url
         * @param int    $max_bytes stop fetching after this amount, defaults to 50kb
         * @param int    $timeout
         */
        function urlfetcher($url, $max_bytes = 52428800, $timeout = 5)
        {
            $this->_url       = $url;
            $this->_max_bytes = $max_bytes;
            $this->_timeout   = $timeout;
        }
        /**
         * @param boolean $text_only          If true, non-text mime-types result in error
         * @param boolean $lazy_mime_checking If false, unknown mime-types result in error
         * @return string|false
         */
        function getContent($text_only = true, $lazy_mime_checking = false)
        {
            $content = '';
            $this->_mime = false;
            $handle = @fopen($this->_url, 'r');
            if($handle)
            {
                stream_set_timeout($handle, $this->_timeout);
                while (!feof($handle))
                {
                    if(strlen($content) > $this->_max_bytes) {
                        $this->_error = 'Fetched max limit of ' . $this->_max_bytes;
                        return false;
                    } elseif($this->_mime == false && strlen($content) >= 1024) {
                        /*
                         * Write what we have so far to a temporary file and check mime type
                         */
                        $tmpfile = tempnam("/tmp", "bambotoo-tmp");
                        $handle2 = fopen($tmpfile, "w");
                        fwrite($handle2, $content);
                        fclose($handle2);
                        $this->_mime = mime_content_type($tmpfile);
                        if(!$this->_mime) {
                            if(!$lazy_mime_checking) {
                               $this->_error = 'Unknown mime-type';
                               return false; 
                            }
                        } elseif(substr($this->_mime, 0, 4) != 'text') {
                            if($text_only) {
                                $this->_error = 'Unhandled mime-type: ' . $this->_mime;
                                return false;
                            }
                        }
                        unlink($tmpfile);
                    }
                    $content .= fgets ($handle, 1024);
                }
                
                if($content != '') {
                    return $content;
                } else {
                    $this->_error = 'Empty file';
                    return false;
                }
            } else {
                $this->_error = 'Could not fetch ' . $this->_url;
                return false;
            }
        }
        function getMime()
        {
            if($this->_mime) {
                return $this->_mime;
            } else {
                return false;
            }
        }
        function getError()
        {
            if($this->_error) {
                return $this->_error;
            } else {
                return false;
            }
        }
    }
?>