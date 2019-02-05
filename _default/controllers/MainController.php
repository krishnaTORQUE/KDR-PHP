<?php

if(!defined('ROOT')) require $_SERVER['ERROR_PATH'];

$this->ROUTE([
    'URL'    => '/',
    'METHOD' => ['get'],
    'FUNC'   => 'home+index'
]);