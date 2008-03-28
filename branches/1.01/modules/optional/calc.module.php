<?php
    /**
     * calc
     * 
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Calc Module
     * 
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     */
    class calc extends module
    {
        var $version = '0.1';
        var $trigger = 'calc';
        var $help    = 'Calculates a math problem';
        var $usage   = '!calc <num1> <operator> <num2>';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';
        
        function call()
        {
            if(strpos($this->buffer->text, ' ') !== false)
                list($text, $math) = explode(' ', $this->buffer->text, 2);
            else
                $math = '';
                
            $this->sendRaw('PRIVMSG ' . $this->buffer->channel[0] . ' :' . $this->math($math));
        }
    
        function math($math)
        {
            if($math) {
                $math = explode(' ', $math);
                $num1 = current($math);
                $op   = next($math);
                $num2 = next($math);
                
                if(is_numeric($num1) && is_numeric($num2) && $op) {
                    switch($op) {
                        case '+':
                            $return = ($num1 + $num2) . ' ';
                            break;
                        case '-':
                            $return = ($num1 - $num2) . ' ';
                            break;
                        case '*':
                            $return = ($num1 * $num2) . ' ';
                            break;
                        case '/':
                            if($num2 == 0)
                                $return = 'Division by zero is not allowed.';
                            else
                                $return = ($num1 / $num2) . ' ';
                            break;
                        default:
                            $return = 'Not a correct formatted math problem.';
                    }   
                    if($return) {
                        $return = trim($return);
                    }
                } else {
                    $return = 'Not a correct formatted math problem.';
                }
            } else {
                $return = $this->getUsage();
            }
            return $return;
        }
    }

?>
