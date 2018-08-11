<?php
/*
 * Default Static Error Page
 * Set Default Static Error Page From Server Config File
 */

while (ob_get_contents()) {
    ob_end_clean();
}
http_response_code(404);
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8"/>
        <title>Error - Not found</title>

        <meta name="description"
              content="Page not found. Removed or Down for Maintain"/>

        <style type="text/css">
            .main {
                color         : #555;
                border        : 8px dashed #777;
                width         : 50%;
                border-radius : 8px;
                margin        : 8% auto;
                padding       : 30px;
                text-align    : center;
            }
        </style>
    </head>

    <body>
    <div class="main">
        <h1>
            Page not found
            <br/><br/>
            Removed or Down for Maintain
        </h1>
    </div>
    </body>
    </html><?php die(); ?>