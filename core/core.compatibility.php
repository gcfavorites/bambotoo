<?php
    /**
     * Compatibility functions
     * For non-existing functions in PHP4
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    
    if(!function_exists('stripos')) {
        /**
         * see http://php.net/stripos
         */
        function stripos($str, $needle, $offset = 0)
        {
            return strpos(strtolower($str), strtolower($needle), $offset);
        }
    }
    if (!function_exists('mime_content_type'))
    {
        /**
         * see http://php.net/mime_content_type
         */
        function mime_content_type($file)
        {
            return trim(exec('file -bi ' . escapeshellarg($file)));
        }
    }
    
?>