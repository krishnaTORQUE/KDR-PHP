<?php

if (!defined('MAIN')) {
    require $_SERVER['KDO_ERROR'];
}

class home {

    public function index($App) {

        $App->FILE('includes/headers', true);

        $App->RENDER([
            'FILE' => $App->VIEW('home')
        ]);
    }

}
