<?php
/**
 * Default Static Error Page
 * Set Default Static Error Page From Server Config File
 */
while(ob_get_contents()) {
    ob_end_clean();
}

$http_code = http_response_code();
if($http_code < 400) {
    $http_code = 404;
}
http_response_code($http_code);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>

    <meta http-equiv="Content-Type"
          content="text/html"/>

    <title><?php echo $http_code; ?> Error</title>

    <meta name="description"
          content="Page not found. Removed or Down for Maintain"/>

    <style type="text/css">
        .main {
            color         : #444444;
            width         : 50%;
            border        : 2px dashed #444444;
            border-radius : 18px;
            margin        : 8% auto;
            padding       : 30px;
            text-align    : center;
            font-family   : Tahoma, Helvetica, Arial, sans-serif;
        }
    </style>
</head>
<body>

<div class="main">

    <h1>
        <?php echo $http_code; ?>
        Error
    </h1>

    <h3>
        Page not found
        <br/><br/>
        Removed or Down for Maintain
    </h3>
</div>

</body>
</html>
<?php if(!defined('ROOT')) die(); ?>
