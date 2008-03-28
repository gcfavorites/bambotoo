<?php
    /**
     * Xmlparser Class
     *
     * @package    bambotoo
     * @subpackage bambotoo-core
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    
    /**
     * Xmlparser Class
     * this is a simple XML parser that takes the tags and values and attributes into an array
     * 
     * @package    bambotoo
     * @subpackage bambotoo-core
     */
    class xmlparser
    {
        /**
         * @var array
         * @access private
         */
        var $_data;
        
        /**
         * @var object
         * @access private
         */
        var $_parser;
        
        /**
         * @var array stack
         */
        var $_stack;
        
        /**
         * @var array current tag
         */
        var $_current;
        
        /**
         * @var string errorstr
         */
        var $_error;
        
        /**
         * Parse this string
         * Returns XML as an array (of arrays (of arrays))
         * @param string $string an XML-documents source
         * @return array|false
         */
        function xml2array($string)
        {
            if(! ($this->_parser = xml_parser_create())) {
                return false;
            }
            $this->_data  = array();
            $this->_stack = array();
            $this->_current =& $this->_data;
            xml_set_object($this->_parser, $this);
            
            // Skip whitespace values
            xml_parser_set_option($this->_parser, XML_OPTION_SKIP_WHITE, 1);
            
            
            xml_set_element_handler($this->_parser, "_start_tag", "_end_tag");
            xml_set_character_data_handler($this->_parser, "_tag_contents");
            if(!xml_parse($this->_parser, $string))
            {
                $this->parseerror =  "XMLERROR:\n\n"
                     .'  CODE: ' . xml_get_error_code($this->_parser) . "\n"
                     .'STRING: ' . xml_error_string(xml_get_error_code($this->_parser)). "\n"
                     .'  LINE: ' . xml_get_current_line_number($this->_parser). "\n"
                     .'   COL: ' . xml_get_current_column_number($this->_parser). "\n\n";
            }
            xml_parser_free($this->_parser);
            
            if($this->_error) {
                return false;
            } elseif($this->_data) {
                return $this->_data;
            } else {
                return false;
            }
        }
        /**
         * Converts a array to xml
         */
        function array2xml($array) {
            $return = '<?xml version="1.0">';
            return false;
        	
        }
        
        /**
         * Parse a xml start tag
         * @param object $parser
         * @param string $starttag
         * @param array  $attr
         */
        function _start_tag($parser, $starttag, $attr)
        {
            if(!$this->_error) {
                if(!isset($this->_current[$starttag])) {
                    $this->_current[$starttag] = array();
                    $this->_current[$starttag][0] = array();
                    $this->_current[$starttag][0]['value'] = '';
                    $i = 0;
                } else {
                    $i = count($this->_current[$starttag]);
                }
                if (count($attr)) {
                    $this->_current[$starttag][$i]['attr'] = array();
                    foreach ($attr as $k => $v) {
                        $this->_current[$starttag][$i]['attr'][$k] = $v;
                    } 
                }
                
                $this->_stack[] = &$this->_current;
                
                if(!is_array($this->_current)) {
                    $this->_error = 'Malformed XML';
                } else {            
                    $this->_current = &$this->_current[$starttag][$i]['value'];
                }
            }
        } 
       /**
         * Parse a xml end tag
         * @param object $parser
         * @param string $endtag
         */
        function _end_tag($parser, $endtag)
        {
            if(!$this->_error) {
                $this->_current = &$this->_stack[count($this->_stack) - 1];
                array_pop($this->_stack);
            }
            
        }
        
       /**
         * Parse  xml content
         * @param object $parser
         * @param string $endtag
         */
        function _tag_contents($parser, $content)
        {
            if(!$this->_error) {
                $content = trim($content);
                if($content) {
                    $this->_current .= $content;
                }
            }
        }
    }
?>