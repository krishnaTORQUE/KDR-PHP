<?php

##    ## ########  ########
##   ##  ##     ## ##     ##
##  ##   ##     ## ##     ##
#####    ##     ## ########
##  ##   ##     ## ##   ##
##   ##  ##     ## ##    ##
##    ## ########  ##     ##

/*
 * ******************************
 * ***     Main Entry Point   ***
 * *** BootLoader / BootStrap ***
 * ******************************
 */

/*
 * **********************
 * *** Setup Constant ***
 * *** Process Files  ***
 * **********************
 */
$mkconst = function($path) {
    $path = str_ireplace('\\', '/', $path);
    $explode = explode('/', $path);
    array_pop($explode);
    return implode('/', $explode) . '/';
};

/*
 * Define Constants
 */
define('ROOT', $mkconst(dirname(__FILE__)));
define('PATH', $mkconst(dirname($_SERVER['SCRIPT_NAME'])));

unset($mkconst);

/*
 * Start
 */
ob_start();
require __DIR__ . '/Sys.php';
require __DIR__ . '/App.php';