<?php
/*
 * Default Footer File
 */
if (!defined('MAIN')) {
    require $_SERVER['ERROR_PATH'];
}

/*
 * Sending App Strings to Client
 */
$http_response_code = http_response_code();
if ($http_response_code >= 200 && $http_response_code < 400 && isset($this->STRINGS)) {
    ?>
    <script defer
            type="text/javascript">

        var STRINGS = false;
        var xmlHttpReq = false;
        window.addEventListener('DOMContentLoaded', function () {
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
                    if (this.status > 199 && this.status < 400) {
                        STRINGS = JSON.parse(this.responseText.trim());
                    }
                }, false);
                xmlHttpReq.send(null);
            }
        }, false);
    </script>
    <?php
}

/*
 * Calling In Footer Function
 */
$this->CALL_FUNCS('IN_FOOTER');
?>
</body>
</html>