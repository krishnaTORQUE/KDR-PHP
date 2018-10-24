<?php

if (!defined('MAIN')) {
    require $_SERVER['ERROR_PATH'];
}

class home {

    public function index($App) {

        $App->ADD_FUNC('IN_HEAD', function ($arg) {

            echo '
            <link href="' . $arg->APP_URL . 'favicon.png?v=' . $arg->APP['PLATFORM']['VERSION'] . '&s=' . $arg->APP['PLATFORM']['STATUS'] . '" rel="shortcut icon" type="image/png"/>
            <link href="' . $arg->APP_URL . 'css/style.css' . '" type="text/css" rel="stylesheet" />                            
            ';
        });

        $App->RENDER([
            'FILE' => $App->VIEW('home')
        ]);
    }

}
