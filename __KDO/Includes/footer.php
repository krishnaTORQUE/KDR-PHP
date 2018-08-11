<?php
if (!defined('MAIN')) {
    require $_SERVER['ERROR_PATH'];
}

/*
 * Default Footer File
 */

/*
 * Sending App Strings to Client
 */
if (isset($this->STRINGS)) {
    ?>
    <script type="text/javascript">
        var STRINGS = false;
        var xmlHttpReq = false;

        if (window.XMLHttpRequest) {
            xmlHttpReq = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            try {
                xmlHttpReq = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (ex) {
                try {
                    xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (ex) {
                }
            }
        }

        if (xmlHttpReq) {
            xmlHttpReq.open('POST', '<?php echo $this->URA['APP'];?>--STRINGS--', true);
            xmlHttpReq.addEventListener('load', function () {
                if (this.status >= 200 && this.status < 400) {
                    STRINGS = JSON.parse(this.responseText.trim());
                }
            }, false);
            xmlHttpReq.send(null);
        }
    </script>
    <?php
}

/*
 * Links to JavaScript Tags
 */
foreach ($this->LOAD_TAGS['JS'] as $js_tags) {
    echo '<script type="text/javascript" src="' . $js_tags . '"></script>' . PHP_EOL;
}
unset($js_tags);

$this->CALL_FUNCS('IN_FOOTER');
?>
    </body>
    </html> <?php $this->CALL_FUNCS('AFTER_FOOTER'); ?>