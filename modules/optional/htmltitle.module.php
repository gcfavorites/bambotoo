<?php
    /**
     * htmltitle
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Htmltitle Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     */
    class htmltitle extends module
    {
        var $version = '0.1';
        var $trigger = '*';
        var $help    = 'Show HTML <title> of url\'s posted.';
        var $usage   = 'Automated';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

        function call()
        {
            if(strpos($this->buffer->text, 'http://') !== false)
            {
                $text = explode(' ', $this->buffer->text);
                foreach($text as $word)
                {
                    if(substr($word, 0,7) == 'http://')
                    {
                        $url = trim($word);
                        break(1);
                    }
                }
                $ret = $this->getTitleTag($url);

                if($ret)
                    $this->send('Title of ' . $this->buffer->nick . '\'s URL: "' . $ret . '"');
            }
        }

        function getTitleTag($url)
        {
            $urlfetcher = new urlfetcher($url);
            $html       = $urlfetcher->getContent(true, true);
            $mime       = $urlfetcher->getMime();

            if($html)
            {
                if (preg_match('/<title>.*<\/title>/', $html, $matches))
                {
                    $match = substr($matches[0], 7, -8);
                    $match = html_entity_decode($match, ENT_QUOTES);
                    $match = str_replace('&rdquo;', '"', $match);
                    $match = trim($match);
                    if($match != '') {
                        return $match;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
    }
?>
