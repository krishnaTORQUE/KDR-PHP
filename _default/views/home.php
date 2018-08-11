<?php
if (!defined('MAIN')) {
    require $_SERVER['KDO_ERROR'];
}
?>
<div class="main">

    <img src="<?php echo $this->APP_URL; ?>favicon.png"/>

    <h2>Welcome to <?php echo $this->APP['PLATFORM']['NAME']; ?></h2>

    <h5>
        Version: <?php echo $this->APP['PLATFORM']['VERSION']; ?>
        &nbsp;
        Status: <?php echo $this->APP['PLATFORM']['STATUS']; ?>
    </h5>

    <h4>URL: <?php echo $this->URA['APP']; ?></h4>

    <h4>ROOT: <?php echo ROOT; ?></h4>

    <h4>PATH: <?php echo PATH; ?></h4>

    <div>Create <code style="font-weight: bold; font-size: 16px;">`Configure.php`</code>
        file in root directory and set Configuration.
    </div>

</div>