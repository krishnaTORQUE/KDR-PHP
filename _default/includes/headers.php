<?php

if (!defined('MAIN')) {
    require $_SERVER['KDO_ERROR'];
}

$this->LOAD_TAGS['ICON']['URL'] = $this->APP_URL . 'favicon.png?v=' . $this->APP['PLATFORM']['VERSION'] . '&s=' . $this->APP['PLATFORM']['STATUS'];
$this->LOAD_TAGS['ICON']['TYPE'] = 'image/png';
array_push($this->LOAD_TAGS['CSS'], $this->APP_URL . 'css/style');
