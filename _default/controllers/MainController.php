<?php

if (!defined('MAIN')) {
    require $_SERVER['KDO_ERROR'];
}

$this->ROUTE([
    'URL' => '/',
    'METHOD' => ['get']
], 'home+index');
