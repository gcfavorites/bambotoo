<?php
/**
    * Smarty -  the PHP templating engines
    *
    * @package    bambotoo-modules
    * @subpackage bambotoo-custom-modules
    * @author     Pete "mash" Morgan <pete_morgan@php.net>
    * @copyright  2006+, Pete "mash" Morgan
    * @license    http://www.gnu.org/copyleft/gpl.html
    */
    /**
     * Smarty Module
     * @package    bambotoo-modules
     * @subpackage bambotoo-custom-modules
     */
    class smarty extends module
    {
        var $version = '0.3-stable';
        var $trigger    = 'smarty';
        var $mode    = USER_MODE;

        var $help       = 'Smarty help';
        var $usage      = '!smarty <keyword>, man, home, download, faq, forums';
        var $credits    = "Peter 'mash' Morgan - pete_morgan@php.net http://smarty.php.net";

        var $smarty_toc = array(); // loaded in constructor below


        function call()
        {
            $find = trim(str_replace('!smarty','',$this->buffer->text));
            $ini = $this->config;

            if($find != '') {
                if($find == 'man' || $find == 'home' || $find == 'download' || $find == 'faq' || $find == 'forum'){
                    switch($find){
                        case 'man':
                            $str = 'Smarty manual - '.$ini['manual'];
                            break;

                        case 'home':
                            $str = 'Smarty home page - '.$ini['home'];
                            break;

                        case 'download':
                            $str = 'Smarty download - '.$ini['download'];
                            break;

                        case 'faq':
                            $str = 'Smarty faq\'s - '.$ini['faq'];
                            break;
                       case 'forum':
                            $str = 'Smarty forum - '.$ini['forum'];
                            break;
                    }

                // find exact match
                }elseif(array_key_exists($find,$this->smarty_toc)) {
                    $str = $find.' '.$ini['manual_url'].str_replace('_','.',$this->smarty_toc[$find]);
                } else {
                    // loop toc and find matching strings into an array
                    $matches = array();
                    foreach($this->smarty_toc as $k => $v) {
                        if(strpos($k,$find) !== false ) {
                            $matches[] = $k;
                        }
                    }
                    // no array means no matches
                    if(count($matches) == 0) {
                        $str = 'No entry found';

                    // only one match found
                    } elseif(count($matches) == 1) {
                        $str = $matches[0].' '.$ini['manual_url'].str_replace('_','.',$this->smarty_toc[$matches[0]]);

                    // more than one match so show list
                    } else {
                        $str = 'Entries: ';
                        asort($matches);
                        foreach($matches as $match) {
                            $str .= $match.', ';
                        }
                        if($str != '') {
                            $str = substr($str,0,-2);
                        }
                    }

                }
            } else {
                $str = $this->getUsage() . ' - ' . $ini['home'];
            }
            $this->send($str);
        }


        function smarty() {
            // Mersci and ta toggg@sf.net the credit for this cut 2 paste

            $this->help = 'Smarty templating: '.$this->usage;
            $this->smarty_toc = array();
            $this->smarty_toc['$smarty'] = 'language.variables.smarty.php';
            $this->smarty_toc['capitalize'] = 'language.modifier.capitalize';
            $this->smarty_toc['cat'] = 'language.modifier.cat.php';
            $this->smarty_toc['count_characters'] = 'language.modifier.count.characters.php';
            $this->smarty_toc['count_paragraphs'] = 'language.modifier.count.paragraphs.php';
            $this->smarty_toc['count_sentences'] = 'language.modifier.count.sentences.php';
            $this->smarty_toc['count_words'] = 'language.modifier.count.words.php';
            $this->smarty_toc['date_format'] = 'language.modifier.date.format.php';
            $this->smarty_toc['default'] = 'language.modifier.default.php';
            $this->smarty_toc['escape'] = 'language.modifier.escape.php';
            $this->smarty_toc['indent'] = 'language.modifier.indent.php';
            $this->smarty_toc['lower'] = 'language.modifier.lower.php';
            $this->smarty_toc['nl2br'] = 'language.modifier.nl2br.php';
            $this->smarty_toc['regex_replace'] = 'language.modifier.regex.replace.php';
            $this->smarty_toc['replace'] = 'language.modifier.replace.php';
            $this->smarty_toc['spacify'] = 'language.modifier.spacify.php';
            $this->smarty_toc['string_format'] = 'language.modifier.string.format.php';
            $this->smarty_toc['strip'] = 'language.modifier.strip.php';
            $this->smarty_toc['strip_tags'] = 'language.modifier.strip.tags.php';
            $this->smarty_toc['truncate'] = 'language.modifier.truncate.php';
            $this->smarty_toc['upper'] = 'language.modifier.upper.php';
            $this->smarty_toc['wordwrap'] = 'language.modifier.wordwrap.php';
            $this->smarty_toc['{capture}'] = 'language.function.capture';
            $this->smarty_toc['{config_load}'] = 'language.function.config.load.php';
            $this->smarty_toc['{foreach},{foreachelse}'] = 'language.function.foreach.php';
            $this->smarty_toc['{include}'] = 'language.function.include.php';
            $this->smarty_toc['{include_php}'] = 'language.function.include.php.php';
            $this->smarty_toc['{insert}'] = 'language.function.insert.php';
            $this->smarty_toc['{if},{elseif},{else}'] = 'language.function.if.php';
            $this->smarty_toc['{ldelim},{rdelim}'] = 'language.function.ldelim.php';
            $this->smarty_toc['{literal}'] = 'language.function.literal.php';
            $this->smarty_toc['{php}'] = 'language.function.php.php';
            $this->smarty_toc['{section},{sectionelse}'] = 'language.function.section.php';
            $this->smarty_toc['{strip}'] = 'language.function.strip.php';
            $this->smarty_toc['{assign}'] = 'language.function.assign';
            $this->smarty_toc['{counter}'] = 'language.function.counter.php';
            $this->smarty_toc['{cycle}'] = 'language.function.cycle.php';
            $this->smarty_toc['{debug}'] = 'language.function.debug.php';
            $this->smarty_toc['{eval}'] = 'language.function.eval.php';
            $this->smarty_toc['{fetch}'] = 'language.function.fetch.php';
            $this->smarty_toc['{html_checkboxes}'] = 'language.function.html.checkboxes.php';
            $this->smarty_toc['{html_image}'] = 'language.function.html.image.php';
            $this->smarty_toc['{html_options}'] = 'language.function.html.options.php';
            $this->smarty_toc['{html_radios}'] = 'language.function.html.radios.php';
            $this->smarty_toc['{html_select_date}'] = 'language.function.html.select.date.php';
            $this->smarty_toc['{html_select_time}'] = 'language.function.html.select.time.php';
            $this->smarty_toc['{html_table}'] = 'language.function.html.table.php';
            $this->smarty_toc['{math}'] = 'language.function.math.php';
            $this->smarty_toc['{mailto}'] = 'language.function.mailto.php';
            $this->smarty_toc['{popup_init}'] = 'language.function.popup.init.php';
            $this->smarty_toc['{popup}'] = 'language.function.popup.php';
            $this->smarty_toc['{textformat}'] = 'language.function.textformat.php';
            $this->smarty_toc['SMARTY_DIR'] = 'constant.smarty.dir';
            $this->smarty_toc['SMARTY_CORE_DIR'] = 'constant.smarty.core.dir.php';
            $this->smarty_toc['$template_dir'] = 'variable.template.dir';
            $this->smarty_toc['$compile_dir'] = 'variable.compile.dir.php';
            $this->smarty_toc['$config_dir'] = 'variable.config.dir.php';
            $this->smarty_toc['$plugins_dir'] = 'variable.plugins.dir.php';
            $this->smarty_toc['$debugging'] = 'variable.debugging.php';
            $this->smarty_toc['$debug_tpl'] = 'variable.debug.tpl.php';
            $this->smarty_toc['$debugging_ctrl'] = 'variable.debugging.ctrl.php';
            $this->smarty_toc['$autoload_filters'] = 'variable.autoload.filters.php';
            $this->smarty_toc['$compile_check'] = 'variable.compile.check.php';
            $this->smarty_toc['$force_compile'] = 'variable.force.compile.php';
            $this->smarty_toc['$caching'] = 'variable.caching.php';
            $this->smarty_toc['$cache_dir'] = 'variable.cache.dir.php';
            $this->smarty_toc['$cache_lifetime'] = 'variable.cache.lifetime.php';
            $this->smarty_toc['$cache_handler_func'] = 'variable.cache.handler.func.php';
            $this->smarty_toc['$cache_modified_check'] = 'variable.cache.modified.check.php';
            $this->smarty_toc['$config_overwrite'] = 'variable.config.overwrite.php';
            $this->smarty_toc['$config_booleanize'] = 'variable.config.booleanize.php';
            $this->smarty_toc['$config_read_hidden'] = 'variable.config.read.hidden.php';
            $this->smarty_toc['$config_fix_newlines'] = 'variable.config.fix.newlines.php';
            $this->smarty_toc['$default_template_handler_func'] = 'variable.default.template.handler.func.php';
            $this->smarty_toc['$php_handling'] = 'variable.php.handling.php';
            $this->smarty_toc['$security'] = 'variable.security.php';
            $this->smarty_toc['$secure_dir'] = 'variable.secure.dir.php';
            $this->smarty_toc['$security_settings'] = 'variable.security.settings.php';
            $this->smarty_toc['$trusted_dir'] = 'variable.trusted.dir.php';
            $this->smarty_toc['$left_delimiter'] = 'variable.left.delimiter.php';
            $this->smarty_toc['$right_delimiter'] = 'variable.right.delimiter.php';
            $this->smarty_toc['$compiler_class'] = 'variable.compiler.class.php';
            $this->smarty_toc['$request_vars_order'] = 'variable.request.vars.order.php';
            $this->smarty_toc['$request_use_auto_globals'] = 'variable.request.use.auto.globals.php';
            $this->smarty_toc['$error_reporting'] = 'variable.error.reporting.php';
            $this->smarty_toc['$compile_id'] = 'variable.compile.id.php';
            $this->smarty_toc['$use_sub_dirs'] = 'variable.use.sub.dirs.php';
            $this->smarty_toc['$default_modifiers'] = 'variable.default.modifiers.php';
            $this->smarty_toc['$default_resource_type'] = 'variable.default.resource.type.php';
            $this->smarty_toc['append()'] = 'api.append.php';
            $this->smarty_toc['append_by_ref()'] = 'api.append.by.ref.php';
            $this->smarty_toc['assign()'] = 'api.assign.php';
            $this->smarty_toc['assign_by_ref()'] = 'api.assign.by.ref.php';
            $this->smarty_toc['clear_all_assign()'] = 'api.clear.all.assign.php';
            $this->smarty_toc['clear_all_cache()'] = 'api.clear.all.cache.php';
            $this->smarty_toc['clear_assign()'] = 'api.clear.assign.php';
            $this->smarty_toc['clear_cache()'] = 'api.clear.cache.php';
            $this->smarty_toc['clear_compiled_tpl()'] = 'api.clear.compiled.tpl.php';
            $this->smarty_toc['clear_config()'] = 'api.clear.config.php';
            $this->smarty_toc['config_load()'] = 'api.config.load.php';
            $this->smarty_toc['display()'] = 'api.display.php';
            $this->smarty_toc['fetch()'] = 'api.fetch.php';
            $this->smarty_toc['get_config_vars()'] = 'api.get.config.vars.php';
            $this->smarty_toc['get_registered_object()'] = 'api.get.registered.object.php';
            $this->smarty_toc['get_template_vars()'] = 'api.get.template.vars.php';
            $this->smarty_toc['is_cached()'] = 'api.is.cached.php';
            $this->smarty_toc['load_filter()'] = 'api.load.filter.php';
            $this->smarty_toc['register_block()'] = 'api.register.block.php';
            $this->smarty_toc['register_compiler_function()'] = 'api.register.compiler.function.php';
            $this->smarty_toc['register_function()'] = 'api.register.function.php';
            $this->smarty_toc['register_modifier()'] = 'api.register.modifier.php';
            $this->smarty_toc['register_object()'] = 'api.register.object.php';
            $this->smarty_toc['register_outputfilter()'] = 'api.register.outputfilter.php';
            $this->smarty_toc['register_postfilter()'] = 'api.register.postfilter.php';
            $this->smarty_toc['register_prefilter()'] = 'api.register.prefilter.php';
            $this->smarty_toc['register_resource()'] = 'api.register.resource.php';
            $this->smarty_toc['trigger_error()'] = 'api.trigger.error.php';
            $this->smarty_toc['template_exists()'] = 'api.template.exists.php';
            $this->smarty_toc['unregister_block()'] = 'api.unregister.block.php';
            $this->smarty_toc['unregister_compiler_function()'] = 'api.unregister.compiler.function.php';
            $this->smarty_toc['unregister_function'] = 'api.unregister.function.php';
            $this->smarty_toc['unregister_modifier()'] = 'api.unregister.modifier.php';
            $this->smarty_toc['unregister_object()'] = 'api.unregister.object.php';
            $this->smarty_toc['unregister_outputfilter()'] = 'api.unregister.outputfilter.php';
            $this->smarty_toc['unregister_postfilter()'] = 'api.unregister.postfilter.php';
            $this->smarty_toc['unregister_prefilter()'] = 'api.unregister.prefilter.php';
            $this->smarty_toc['unregister_resource()'] = 'api.unregister.resource.php';
        } // end constructor

    }

?>
