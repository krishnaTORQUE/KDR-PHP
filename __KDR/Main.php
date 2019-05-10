<?php

##    ## ########  ########
##   ##  ##     ## ##     ##
##  ##   ##     ## ##     ##
#####    ##     ## ########
##  ##   ##     ## ##   ##
##   ##  ##     ## ##    ##
##    ## ########  ##     ##

/**
 * ************************
 * *** Main Entry Point ***
 * ************************
 */

/**
 * Making Constant
 */
$mkconst = function($path) {
    $path = str_ireplace('\\', '/', $path);
    $explode = explode('/', $path);
    array_pop($explode);
    return implode('/', $explode) . '/';
};

/**
 * Define Constants
 */
define('ROOT', $mkconst(__DIR__));
define('PATH', $mkconst(dirname($_SERVER['SCRIPT_NAME'])));

$mkconst = null;
unset($mkconst);

/**
 * Start App
 */
ob_start();
require __DIR__ . '/Sys.php';
require __DIR__ . '/App.php';
