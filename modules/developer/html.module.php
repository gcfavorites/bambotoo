<?php
    /**
     * html
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     * @author     Peter "mash" Morgam <pedromorgan@gmail.com>
     * @copyright  2006, Tobias Nystrom
     * @license    http://www.gnu.org/copyleft/gpl.html
     */
    /**
     * HTML Module - Links to HTML reference
     *
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     */
    class html extends module
    {
        var $version = '0.6-stable';
        var $trigger = 'html';

        var $help    = 'Links to xHTML reference';
        var $usage   = '!html <element>';
        var $credits = 'Created by Peter "mash" Morgan <pedromorgan@gmail.com>, part of bambotoo. http://www.lejban.se/bambotoo/';
        var $html;

        function call()
        {
            $url = $this->config['html_url'];
            if($this->arguments){
                $ele = strtoupper($this->args[1]);
                if(array_key_exists($ele, $this->html)){
                    $return = '<'.strtolower($ele).'> - '.$url.$this->html[$ele][0];
                }else{
                    $return = 'Element <'.$ele.'> not found';
                }
            }else{
                $return = 'Missing element. '.$this->getUsage().'. Reference at '.$url;
            }
            $this->send($return);
        }


        function html(){
            $this->html['A'] = array('special/a.html','Anchor');
            $this->html['ABBR'] = array('phrase/abbr.html','Abbreviation');
            $this->html['ACRONYM'] = array('phrase/acronym.html','Acronym');
            $this->html['ADDRESS'] = array('block/address.html','Address');
            $this->html['APPLET'] = array('special/applet.html','Java applet');
            $this->html['AREA'] = array('special/area.html','Image map region');
            $this->html['B'] = array('fontstyle/b.html','Bold text');
            $this->html['BASE'] = array('head/base.html','Document base URI');
            $this->html['BASEFONT'] = array('special/basefont.html','Base font change');
            $this->html['BDO'] = array('special/bdo.html','BiDi override');
            $this->html['BIG'] = array('fontstyle/big.html','Large text');
            $this->html['BLOCKQUOTE'] = array('block/blockquote.html','Block quotation');
            $this->html['BODY'] = array('html/body.html','Document body');
            $this->html['BR'] = array('special/br.html','Line break');
            $this->html['BUTTON'] = array('forms/button.html','Button');
            $this->html['CAPTION'] = array('tables/caption.html','Table caption');
            $this->html['CENTER'] = array('block/center.html','Centered block');
            $this->html['CITE'] = array('phrase/cite.html','Citation');
            $this->html['CODE'] = array('phrase/code.html','Computer code');
            $this->html['COL'] = array('tables/col.html','Table column');
            $this->html['COLGROUP'] = array('tables/colgroup.html','Table column group');
            $this->html['DD'] = array('lists/dd.html','Definition description');
            $this->html['DEL'] = array('phrase/del.html','Deleted text');
            $this->html['DFN'] = array('phrase/dfn.html','Defined term');
            $this->html['DIR'] = array('lists/dir.html','Directory list');
            $this->html['DIV'] = array('block/div.html','Generic block-level container');
            $this->html['DL'] = array('lists/dl.html','Definition list');
            $this->html['DT'] = array('lists/dt.html','Definition term');
            $this->html['EM'] = array('phrase/em.html','Emphasis');
            $this->html['FIELDSET'] = array('forms/fieldset.html','Form control group');
            $this->html['FONT'] = array('special/font.html','Font change');
            $this->html['FORM'] = array('forms/form.html','Interactive form');
            $this->html['FRAME'] = array('frames/frame.html','Frame');
            $this->html['FRAMESET'] = array('frames/frameset.html','Frameset');
            $this->html['H1'] = array('block/h1.html','Level-one heading');
            $this->html['H2'] = array('block/h2.html','Level-two heading');
            $this->html['H3'] = array('block/h3.html','Level-three heading');
            $this->html['H4'] = array('block/h4.html','Level-four heading');
            $this->html['H5'] = array('block/h5.html','Level-five heading');
            $this->html['H6'] = array('block/h6.html','Level-six heading');
            $this->html['HEAD'] = array('head/head.html','Document head');
            $this->html['HR'] = array('block/hr.html','Horizontal rule');
            $this->html['HTML'] = array('html/html.html','HTML document');
            $this->html['I'] = array('fontstyle/i.html','Italic text');
            $this->html['IFRAME'] = array('special/iframe.html','Inline frame');
            $this->html['IMG'] = array('special/img.html','Inline image');
            $this->html['INPUT'] = array('forms/input.html','Form input');
            $this->html['INS'] = array('phrase/ins.html','Inserted text');
            $this->html['ISINDEX'] = array('block/isindex.html','Input prompt');
            $this->html['KBD'] = array('phrase/kbd.html','Text to be input');
            $this->html['LABEL'] = array('forms/label.html','Form field label');
            $this->html['LEGEND'] = array('forms/legend.html','Fieldset caption');
            $this->html['LI'] = array('lists/li.html','List item');
            $this->html['LINK'] = array('head/link.html','Document relationship');
            $this->html['MAP'] = array('special/map.html','Image map');
            $this->html['MENU'] = array('lists/menu.html','Menu list');
            $this->html['META'] = array('head/meta.html','Metadata');
            $this->html['NOFRAMES'] = array('frames/noframes.html','Frames alternate content');
            $this->html['NOSCRIPT'] = array('block/noscript.html','Alternate script content');
            $this->html['OBJECT'] = array('special/object.html','Object');
            $this->html['OL'] = array('lists/ol.html','Ordered list');
            $this->html['OPTGROUP'] = array('forms/optgroup.html','Option group');
            $this->html['OPTION'] = array('forms/option.html','Menu option');
            $this->html['P'] = array('block/p.html','Paragraph');
            $this->html['PARAM'] = array('special/param.html','Object parameter');
            $this->html['PRE'] = array('block/pre.html','Preformatted text');
            $this->html['Q'] = array('special/q.html','Short quotation');
            $this->html['S'] = array('fontstyle/s.html','Strike-through text');
            $this->html['SAMP'] = array('phrase/samp.html','Sample output');
            $this->html['SCRIPT'] = array('special/script.html','Client-side script');
            $this->html['SELECT'] = array('forms/select.html','Option selector');
            $this->html['SMALL'] = array('fontstyle/small.html','Small text');
            $this->html['SPAN'] = array('special/span.html','Generic inline container');
            $this->html['STRIKE'] = array('fontstyle/strike.html','Strike-through text');
            $this->html['STRONG'] = array('phrase/strong.html','Strong emphasis');
            $this->html['STYLE'] = array('head/style.html','Embedded style sheet');
            $this->html['SUB'] = array('special/sub.html','Subscript');
            $this->html['SUP'] = array('special/sup.html','Superscript');
            $this->html['TABLE'] = array('tables/table.html','Table');
            $this->html['TBODY'] = array('tables/tbody.html','Table body');
            $this->html['TD'] = array('tables/td.html','Table data cell');
            $this->html['TEXTAREA'] = array('forms/textarea.html','Multi-line text input');
            $this->html['TFOOT'] = array('tables/tfoot.html','Table foot');
            $this->html['TH'] = array('tables/th.html','Table header cell');
            $this->html['THEAD'] = array('tables/thead.html','Table head');
            $this->html['TITLE'] = array('head/title.html','Document title');
            $this->html['TR'] = array('tables/tr.html','Table row');
            $this->html['TT'] = array('fontstyle/tt.html','Teletype text');
            $this->html['U'] = array('fontstyle/u.html','Underlined text');
            $this->html['UL'] = array('lists/ul.html','Unordered list');
            $this->html['VAR'] = array('phrase/var.html','Variable');

        }
    }

?>
