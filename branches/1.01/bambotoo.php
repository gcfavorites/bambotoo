#!/usr/bin/php
<?php
/**
 * @package    bambotoo
 * @subpackage bambotoo-core
 * @copyright  Copyright 2005-2006, Tobias Nystrom
 * @author     Tobias Nystrom <lejban@gmail.com>
 * @author     Pete "mash" Morgan <pedromorgan@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html
*/
error_reporting(E_ALL);
/*
 * This is the bambotoo.php startup shell script
 */
$APP_NAME = 'Bambotoo';

/*
 * Get the command line arguments into a PHP array
 */
$args = $_SERVER['argv'];

/*
 * Run bambotoo from shell only. Not mod_php (and yes, people attempted !)
 */
if(php_sapi_name() != 'cli') {
    die("Fatal Error: $APP_NAME can only be run from the command line or a shell interface\n"
       ."see INTSALL for more info\n\n");
}
/**
 * include main bambotoo object and initiate it
 */
require_once('core/bambotoo.class.php');

/*
 * Initiate the bot!
 */
$bambotoo = new bambotoo();
$this_file = basename(__FILE__);

/*
 * Show help
 */
if (in_array('-h',$args) || in_array('help',$args) || in_array('--help',$args)) {
    $default_config = $bambotoo->getVar('config_file');
    $default_modules_dir = $bambotoo->getVar('modules_config_dir');
    $usage_str = "usage: $this_file [options]\n"
                ."    -h, help       - your looking at it\n"
                ."    -v, version    - show version\n"
                ."    -d             - run bambotoo in background\n"
                ."    -c <file>      - use the bot config file at <file>\n"
                ."                     defaults to '$default_config'\n"
                ."    -m <dir>       - use the modules in directory at <file>\n"
                ."                     defaults to '$default_modules_dir'\n"
                ."\n"
                ;
    echo $usage_str;
    exit(0);
}
/*
 * Show version
 */
if (in_array('-v',$args) || in_array('version',$args)) {
    echo APP_NAME.'-'.APP_VERSION."\n";
    exit(0);
}

/*
 * Set config file
 */
if (in_array('-c',$args)) {
        $flip = array_flip($args);
        $key = $flip['-c'] + 1;
        if(array_key_exists($key,$args)) {
            $bambotoo->setVar('config_file',$args[$key]);
        } else {
            die("Error: missing config file argument: -c <file>\n");
        }
}
if (in_array('-m',$args)) {
        $flip = array_flip($args);
        $key = $flip['-m'] + 1;
        if(array_key_exists($key,$args)) {
            $bambotoo->setVar('modules_config_dir', $args[$key]);
        } else {
            die("Error: missing modules config directory argument: -m <dir>\n");
        }
}
if (in_array('-d',$args)) {
    require_once('core/core.daemon.class.php');
    $daemon = new daemon();
    if($daemon->daemonize()) {
        $bambotoo->disableAllConsoleOutput();
    } else {
        die($daemon->getError() . "\n");
    }
} else {
    $bambotoo->showLogo();
}

/*
 * Main application loop -  Show time :-)
 */
$bambotoo->run();

?>