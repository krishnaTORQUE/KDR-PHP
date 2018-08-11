<?php

/*

KKKKKKKKK    KKKKKKKDDDDDDDDDDDDD             OOOOOOOOO     
K:::::::K    K:::::KD::::::::::::DDD        OO:::::::::OO   
K:::::::K    K:::::KD:::::::::::::::DD    OO:::::::::::::OO 
K:::::::K   K::::::KDDD:::::DDDDD:::::D  O:::::::OOO:::::::O
KK::::::K  K:::::KKK  D:::::D    D:::::D O::::::O   O::::::O
  K:::::K K:::::K     D:::::D     D:::::DO:::::O     O:::::O
  K::::::K:::::K      D:::::D     D:::::DO:::::O     O:::::O
  K:::::::::::K       D:::::D     D:::::DO:::::O     O:::::O
  K:::::::::::K       D:::::D     D:::::DO:::::O     O:::::O
  K::::::K:::::K      D:::::D     D:::::DO:::::O     O:::::O
  K:::::K K:::::K     D:::::D     D:::::DO:::::O     O:::::O
KK::::::K  K:::::KKK  D:::::D    D:::::D O::::::O   O::::::O
K:::::::K   K::::::KDDD:::::DDDDD:::::D  O:::::::OOO:::::::O
K:::::::K    K:::::KD:::::::::::::::DD    OO:::::::::::::OO 
K:::::::K    K:::::KD::::::::::::DDD        OO:::::::::OO   
KKKKKKKKK    KKKKKKKDDDDDDDDDDDDD             OOOOOOOOO  

                    Version 1.0
*/

/*
 * *************************************
 * *** Main / BootLoader / BootStrap ***
 * *************************************
 */

/*
 * **********************
 * *** Setup Constant ***
 * **********************
 */
$create_const = function ($path) {
    $path = str_ireplace('\\', '/', $path);
    $explode = explode('/', $path);
    array_pop($explode);
    return implode('/', $explode) . '/';
};

define('ROOT', $create_const(dirname(__FILE__)));
define('PATH', $create_const(dirname($_SERVER['SCRIPT_NAME'])));

unset($create_const);

/*
 * ********************
 * *** Starting App ***
 * ********************
 */
define('MAIN', true);
ob_start();

require ROOT . '__KDO/Sys.php';
require ROOT . '__KDO/App.php';
