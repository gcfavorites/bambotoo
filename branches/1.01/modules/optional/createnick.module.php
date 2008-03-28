<?php
    /**
     * createnick
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     * @author     Tobias Nystrom <lejban@gmail.com>
     * @copyright  2005-2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * Createnick Module
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-optional-modules
     */
    class createnick extends module
    {
        var $version = '0.1';
        var $trigger = 'createnick';
        var $help    = 'Random nick generator.';
        var $usage   = '!nickname [<length>]';
        var $credits = 'Created by Tobias Nystrom, part of bambotoo. http://www.lejban.se/bambotoo/';

        function call()
        {
            if(strpos($this->buffer->text, ' ') !== false)
                list($text, $len) = explode(' ', $this->buffer->text, 2);
            else
                $len = 0;

            if($len > 0 && $len < 20 && is_numeric($len))
                $len = floor($len);
            else
                $len = rand(4,9);

            $this->send('Generated nickname: ' . $this->nick($len) . "\n");
        }
        function nick($len)
        {
            $a = array('a','e','i','o','u','y');
            $b = array('b','c','d','f','g','h','j','k','l','m','n','p','q','r','s','t','v','w','x','z');
            $c = array('b','d','f','g','j','l','m','n','p','r','s','t');

            $name = array();
            for($i = 0; $i < $len; $i++)
            {
                $type = 0;
                if($i != 0 && in_array($name[$i - 1], $a))
                    $type = 2;
                elseif($i != 0 && $i < 2 && in_array($name[$i - 1], $b))
                    $type = 1;
                elseif($i != 0 && $i > $len - 2 && in_array($name[$i - 1], $c) && rand(0,4) == 0)
                    $name[$i] = $name[$i - 1];
                elseif($i != 0 && $i > $len - 2 && in_array($name[$i - 1], $b))
                    $type = 1;
                elseif($i > 1 && in_array($name[$i - 1], $b) && in_array($name[$i - 2], $b))
                    $type = 1;
                elseif($i != 0 && in_array($name[$i - 1], $c)  && rand(0,4) == 0)
                    $name[$i] = $name[$i - 1];
                else
                    $type = rand(1,2);

                if($type == 2)
                    $name[$i] = $b[rand(0,19)];
                elseif($type == 1)
                    $name[$i] = $a[rand(0,5)];
            }
            $nick = join($name);
            return ucfirst(strtolower($nick));
        }
    }
?>
