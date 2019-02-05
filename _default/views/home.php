<?php if(!defined('ROOT')) require $_SERVER['ERROR_PATH']; ?>

<div class="main">

    <img src="{{ $this->APP_URL }}assets/favicon.png?v={{ $this->APP['PLATFORM']['VERSION'].$this->APP['PLATFORM']['STATUS'] }}"/>

    <div class="details">
        [
        v{{ $this->APP['PLATFORM']['VERSION'] }}
        :
        <i>{{ $this->APP['PLATFORM']['STATUS'] }}</i>
        ]
    </div>

    <div class="details">
        <i>URL</i>: {{ APP }}
    </div>

    <div class="details">
        <i>ROOT</i>: {{ ROOT }}
    </div>

    <div class="details">
        <i>PATH</i>: {{ PATH }}
    </div>

    <div class="details">
        Create <code class="code">`_config.php`</code>
        file in root directory and set Configuration.
    </div>

</div>
