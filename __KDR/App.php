<?php

if(!defined('ROOT')) require $_SERVER['ERROR_PATH'];

class App extends Sys {

    public $APP_URL;
    public $APP_PATH;
    private $ROUTE_MATCH = false;
    private $PAYLOAD = '';

    /*
     * **************************************
     * *** Setting Up Configs & Variables ***
     * **************************************
     */
    public function RUN() {

        /*
         * Getting The Configuration
         */
        if(is_file(ROOT . '_config.php')) {
            require ROOT . '_config.php';
        }

        /*
         * ************************************
         * *** Initialize The Configuration ***
         * ************************************
         */

        /*
         * Setting up Environment
         */
        if($this->APP['PUBLISH']) {
            error_reporting(0);
            ini_set('display_errors', 0);

        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        /*
         * Load URI & Server Variables
         */
        $this->URI();
        $this->SERVER();

        /*
         * Get Active App Directory
         */
        $this->DIR['APP'] = '_' . $this->APP['ACTIVE'] . '/';

        /*
         * ****************
         * *** Security ***
         * ****************
         */

        /*
         * Default Memory Limit
         */
        ini_set('memory_limit', $this->APP['MEMORY_LIMIT']);

        /*
         * Checking Bad UserAgent
         */
        if(strlen($this->HSA['USERAGENT']) < 6 ||
            strtolower($this->HSA['USERAGENT']) === strtolower('Mozilla/5.0') ||
            preg_match('@\/\.[\w\d]+|\.\/[\w\d]+@', $this->URA['FULL'])) {

            $this->APP['SECURITY']['BAD_AGENT'] = true;

            /*
             * Blocking If Found
             */
            if($this->APP['SECURITY']['BAD_AGENT_BLOCK']) {
                $this->RENDER([
                    'CODE'  => 403,
                    'ERROR' => true
                ]);
            }
        }

        /*
         * Blocking Large URL/URI
         */
        if($this->APP['SECURITY']['MAX_URI_CHAR'] &&
            strlen($this->URA['FULL']) > $this->APP['SECURITY']['MAX_URI_CHAR']) {

            $this->RENDER([
                'CODE'  => 414,
                'ERROR' => true
            ]);
        }

        /*
         * Secure Cookies Sessions
         */
        if(strtolower($this->URA['PROTOCOL']) === 'https') {
            ini_set('session.cookie_secure', 1);
        }

        if($this->APP['SECURITY']['COOKIE_HTTP'] === true) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
        }

        /*
         * *******************************
         * *** Progress After Security ***
         * *******************************
         */

        /*
         * For Maintain
         */
        if($this->MAINTAIN()) {
            $this->RENDER([
                'CODE'  => 503,
                'ERROR' => true
            ]);
        }

        /*
         * Default Timezone
         */
        if($this->APP['TIMEZONE']) {
            date_default_timezone_set($this->APP['TIMEZONE']);
        }

        /*
         * Setup Global Variable & Array
         */
        $this->APP_URL = $this->URA['APP'] . $this->DIR['APP'];
        $this->APP_PATH = ROOT . $this->DIR['APP'];

        $this->AFA = array_merge($this->AFA, [
            'APP_URL'  => $this->URA['APP'] . $this->DIR['APP'],
            'APP_PATH' => ROOT . $this->DIR['APP']
        ]);

        $this->AFA = array_merge($this->AFA, $this->APP);
        $this->AFA = array_merge($this->AFA, $this->MTTR);
        $this->AFA = array_merge($this->AFA, $this->GLOB);
        $this->AFA = array_merge($this->AFA, $this->DIR);
        $this->AFA = array_merge($this->AFA, $this->URA);
        $this->AFA = array_merge($this->AFA, $this->HSA);

        /*
         * Making App Strings if exists
         */
        if($this->APP['STRINGS']) {
            require $this->APP_PATH . $this->APP['STRINGS'] . '.php';
            $strings = new \STRINGS($this);
            $this->STRINGS = $strings->DATA;
            unset($strings);
        }

        /*
         * Getting Plugins & Autoloads
         */
        $this->PLUGS();

        $this->MTTR['TITLE'] = $this->APP['NAME'];

        /*
         * Getting the App Main Controller
         */
        if(is_file($this->APP_PATH . 'controllers/MainController.php')) {

            /*
             * Include Main Controller
             */
            require $this->APP_PATH . 'controllers/MainController.php';

            /*
             * Calling Function After Route
             * Not callable when using / calling RENDER Method
             */
            $this->CALL_FUNCS('AFTER_ROUTE');

        } else {

            /*
             * Error if Main Controller not found
             */
            $this->RENDER([
                'CODE'  => 503,
                'ERROR' => true
            ]);
        }

        /*
         * Error if no Route Match
         */
        if(!$this->ROUTE_MATCH) {
            $this->RENDER([
                'CODE'  => 404,
                'ERROR' => true
            ]);
        }
    }

    /*
     * **********************************
     * *** Adding Plugins & Autoloads ***
     * **********************************
     */
    private function PLUGS() {
        $plug_path = ROOT . $this->DIR['PLUGS'];

        if(is_dir($plug_path)) {

            $plugs_list = scandir($plug_path);

            foreach($plugs_list as $plug) {

                if($plug === '.' || $plug === '..') {
                    continue;
                }

                $plug_file = $plug_path . $plug;
                $plug_f = explode('.', $plug);
                if(is_file($plug_file) && in_array($plug_f[0], $this->APP['PLUGS'])) {
                    require $plug_file;
                }
            }
        }
        unset($plug_path, $plugs_list, $plug, $plug_file);
    }

    /**
     * *********************
     * *** Add Functions ***
     * *********************
     *
     * @value array
     *
     * e.g. array_push($this->ADD_FUNC['IN_HEAD'], 'function_name');
     */
    public $ADD_FUNC = [
        'BEFORE_HEAD'  => [],
        'IN_HEAD'      => [],
        'IN_BODY'      => [],
        'IN_FOOTER'    => [],
        'AFTER_FOOTER' => [],
        'AFTER_ROUTE'  => []
    ];

    /**
     * **********************************
     * *** Alter Native Add Functions ***
     * **********************************
     *
     * @param string $where Location to call
     * @param string $func  Name of the Function or Direct Function
     *
     * e.g. $this->ADD_FUNC('IN_HEAD', 'function_name';
     * e.g. $this->ADD_FUNC('IN_HEAD', function () {});
     *
     * @return void
     */
    public function ADD_FUNC($where, $func) {
        array_push($this->ADD_FUNC[$where], $func);
    }

    /**
     * ******************************
     * *** Calling Added Function ***
     * ******************************
     *
     * ## For System Only ##
     */
    protected function CALL_FUNCS($para) {
        if(array_key_exists($para, $this->ADD_FUNC)) {
            foreach($this->ADD_FUNC[$para] as $func) {
                if(is_callable($func)) {
                    $func($this);
                } else {
                    echo $func;
                }
            }
        }
    }

    /**
     * ***********************************
     * *** Adding View / Template File ***
     * ***********************************
     *
     * @param string $name              Only Name of the file with is located in App `views` directory
     * @param array  $assign            Assign Variables for View File
     * @param array  $arr               Options
     *                                  $arr[xt]        Extension of the blade file
     *                                  $arr[no-tag]    Disable Converting
     *                                  $arr[e]         Execute Now
     *                                  $arr[location]  Location of the file.
     *                                  NOTE: Leave Empty $name
     *                                  e.g. 'path/to/blade/file'
     *
     * @return string
     */
    public function BLADE($name, $assign = [], $arr = []) {

        if(!isset($arr['xt'])) {
            $arr['xt'] = $this->APP['BLADE_XT'];
        }

        $content = '';
        $this->AFA = array_merge($this->AFA, $assign);

        $path = $this->APP_PATH . 'views/' . $name . $arr['xt'];
        if(isset($arr['location'])) {
            $path = $arr['location'] . $arr['xt'];
        }

        if(is_file($path)) {

            $content = file_get_contents($path);

            /*
             * Disable Convert
             */
            if(!isset($arr['no-tag'])) {

                /*
                 * Print Value
                 */
                foreach($this->AFA as $key => $val) {
                    if(isset($key, $val) &&
                        !is_array($val) &&
                        strlen($key) > 0 &&
                        preg_match('@\{\{ ' . $key . ' \}\}@', $content)) {

                        $content = preg_replace('@\{\{ ' . $key . ' \}\}@i', $val, $content);
                    }
                }

                /*
                 * Method & Function
                 */
                $search = [
                    '@\{\( if (.*?) \)\}@i',
                    '@\{\( elif (.*?) \)\}@i',
                    '@\{\( else \)\}@i',
                    '@\{\( endif \)\}@i',
                    '@\{\( each (.*?) \)\}@i',
                    '@\{\( endeach \)\}@i',
                    '@\{\( for (.*?) \)\}@i',
                    '@\{\( endfor \)\}@i',
                    '@\{\( while (.*?) \)\}@i',
                    '@\{\( endwhile \)\}@i',
                    '@\{\{ (.*?) \}\}@i',
                    '@\{\( (.*?) \)\}@i'
                ];

                $replace = [
                    '<?php if($1): ?>',
                    '<?php elseif($1): ?>',
                    '<?php else: ?>',
                    '<?php endif; ?>',
                    '<?php foreach($1): ?>',
                    '<?php endforeach; ?>',
                    '<?php for($1): ?>',
                    '<?php endfor; ?>',
                    '<?php while($1): ?>',
                    '<?php endwhile; ?>',
                    '<?php echo $1; ?>',
                    '<?php $1; ?>'
                ];

                $content = preg_replace($search, $replace, $content);
            }
        }

        $this->PAYLOAD .= $content;

        if(isset($arr['e'])) {
            eval(' ?>' . $content . '<?php ');
        }

        return $content;
    }

    /**
     * ***************************
     * *** Main Routing Method ***
     * ***************************
     *
     * @param array $arr                  Options
     *                                    $arr[url]               Request URL
     *                                    e.g. Static URL          : /contact
     *                                    e.g. Dynamic/Slug URL    : /user/{usr_id}
     *
     *        array $arr[METHOD]          Methods
     *        array $arr[SCHEME]          Schemes
     *              $arr[FROM]            Referer From
     *                                    in          > Request only from inbound
     *                                    out         > Request only from outbound
     *                                    ''|blank    > Request from any
     *
     *        array $arr[FROM_HOST]       Referer Hosts
     *        bool  $arr[QUERY_STR]       Allow / Deny Query String
     *        bool  $arr[X-REQUEST]       Allow / Deny X-Request
     *
     * @return void
     */
    public function ROUTE($arr = []) {

        if($this->ROUTE_MATCH) {
            return;
        }

        if(!isset($arr['FUNC'])) {
            $arr['FUNC'] = false;
        }

        if(!isset($arr['METHOD'])) {
            $arr['METHOD'] = $this->APP['REQUEST']['METHOD'];
        }

        if(!isset($arr['SCHEME'])) {
            $arr['SCHEME'] = $this->APP['REQUEST']['SCHEME'];
        }

        if(!isset($arr['QUERY_STR'])) {
            $arr['QUERY_STR'] = $this->APP['REQUEST']['QUERY_STR'];
        }

        if(!isset($arr['X_REQUEST'])) {
            $arr['X_REQUEST'] = $this->APP['REQUEST']['X_REQUEST'];
        }

        /*
         * Static & Dynamic / Slug URL match with given Route
         */
        $url = explode('/', $this->TRIMS($arr['URL']));
        $slug = [];
        $fpath = '';

        /*
         * Arrange URL with Route
         */
        for($i = 0; $i < count($url); $i++) {

            if(!array_key_exists($i, $this->URA['PATHS'])) {
                break;
            }

            preg_match_all('@\{(.*?)\}@i', $url[$i], $url_match);

            /*
             * Match with Slug
             */
            if(array_key_exists(0, $url_match[0])) {
                $slug[$url_match[1][0]] = $this->URA['PATHS'][$i];
                $fpath .= '/' . $url_match[0][0];

                /*
                 * Match with String
                 */
            } elseif(strtolower($url[$i]) === strtolower($this->URA['PATHS'][$i])) {
                $fpath .= '/' . $this->URA['PATHS'][$i];
            }
        }

        /*
         * URL Matched With Given Route
         */
        if(strtolower($arr['URL']) === strtolower($fpath) &&
            count($url) === count($this->URA['PATHS'])) {

            /*
             * Request From
             */
            if(isset($arr['FROM'])) {
                /*
                 * Custom
                 */
                if(!isset($this->HSA['REFERER_FROM']) ||
                    $this->HSA['REFERER_FROM'] !== $arr['FROM']) {

                    $this->RENDER([
                        'CODE'  => 403,
                        'ERROR' => true
                    ]);
                }

            } elseif(strlen($this->APP['REQUEST']['FROM']) > 0) {
                /*
                 * Config
                 */
                if(!isset($this->HSA['REFERER_FROM']) ||
                    $this->HSA['REFERER_FROM'] !== $this->APP['REQUEST']['FROM']) {

                    $this->RENDER([
                        'CODE'  => 403,
                        'ERROR' => true
                    ]);
                }
            }

            /*
             * Request From Host
             */
            if(isset($arr['FROM_HOST'])) {
                /*
                 * Custom
                 */
                if(!isset($this->HSA['REFERER_HOST']) ||
                    !preg_grep('@' . $this->HSA['REFERER_HOST'] . '@i', $arr['FROM_HOST'])) {

                    $this->RENDER([
                        'CODE'  => 403,
                        'ERROR' => true
                    ]);
                }

            } elseif(is_array($this->APP['REQUEST']['FROM_HOST'])) {
                /*
                 * Config
                 */
                if(!isset($this->HSA['REFERER_HOST']) ||
                    !preg_grep('@' . $this->HSA['REFERER_HOST'] . '@i', $this->APP['REQUEST']['FROM_HOST'])) {

                    $this->RENDER([
                        'CODE'  => 403,
                        'ERROR' => true
                    ]);
                }
            }

            /*
             * Request Method
             */
            if(!preg_grep('@' . $this->HSA['METHOD'] . '@i', $arr['METHOD'])) {
                $this->RENDER([
                    'CODE'  => 405,
                    'ERROR' => true
                ]);
            }

            /*
             * Request Schema
             */
            if(!preg_grep('@' . $this->HSA['SCHEME'] . '@i', $arr['SCHEME'])) {
                $this->RENDER([
                    'CODE'  => 405,
                    'ERROR' => true
                ]);
            }

            /*
             * Query String
             */
            if(!$arr['QUERY_STR']) {
                if(strlen($this->URA['QUERY'] > 0) || count($this->URA['QUERIES']) > 0) {
                    $this->RENDER([
                        'CODE'  => 404,
                        'ERROR' => true
                    ]);
                }
            }

            /*
             * X REQUEST
             */
            if(!$arr['X_REQUEST'] && $this->HSA['X_REQUEST']) {
                $this->RENDER([
                    'CODE'  => 404,
                    'ERROR' => true
                ]);
            }

            /*
             * App Strings Send to Client
             */
            if(strtoupper($arr['URL']) === '/--STRINGS--' &&
                isset($this->HSA['REFERER_FROM']) &&
                strtolower($this->HSA['REFERER_FROM']) === 'in' &&
                strtolower($this->HSA['METHOD']) === 'post') {

                $this->ROUTE_MATCH = true;
                if(isset($this->STRINGS)) {
                    echo json_encode($this->STRINGS, JSON_FORCE_OBJECT);
                }

                return;
            }

            foreach($slug as $key => $value) {

                /*
                 * Trim Slug URL
                 */
                if(isset($arr['TRIM']) && is_array($arr['TRIM']) && array_key_exists($key, $arr['TRIM'])) {
                    $slug[$key] = preg_replace($arr['TRIM'][$key], '', $value);
                }

                /*
                 * Match Slug URL
                 */
                if(isset($arr['MATCH']) && is_array($arr['MATCH']) && array_key_exists($key, $arr['MATCH'])) {

                    if(preg_match($arr['MATCH'][$key], $value)) {
                        continue;
                    } else {
                        $slug = 'wrong';
                        break;
                    }
                }
            }

            if($slug === 'wrong') {
                return;
            }

            /*
             * Callback to App Controller
             */
            $this->ROUTE_MATCH = true;
            $this->ROUTE_CALLBACK($arr['FUNC'], $slug);
        }
    }

    /*
     * **********************
     * *** Route Callback ***
     * **********************
     */
    private function ROUTE_CALLBACK($cb, $pass_arg = false) {

        if(is_string($cb)) {

            /*
             * Callback is a Class
             */

            $obj = explode('+', $cb);

            require $this->APP_PATH . 'controllers/' . $obj[0] . '.php';

            $call = new $obj[0]($this, $pass_arg);

            if(array_key_exists(1, $obj)) {
                $method = $obj[1];
                $call->$method($this, $pass_arg);
            }

        } elseif(is_callable($cb)) {

            /*
             * Callback is Function
             */

            $cb($this, $pass_arg);

        } else {

            /*
             * Nothing Match
             */

            $this->RENDER([
                'CODE'  => 404,
                'ERROR' => true
            ]);
        }
    }

    /**
     * *******************************
     * *** Final Payload Rendering ***
     * *******************************
     *
     * @param array $arr Options
     *
     *          int   $arr[CODE]          Response Code
     *          bool  $arr[ERROR]         Is Error
     *                $arr[TITLE]         Title
     *                $arr[DESCRIPTION]   Meta Description
     *                $arr[KEYWORDS]      Meta Keywords
     *                $arr[HEADER]        Header File Path
     *                $arr[FOOTER]        Footer File Path
     *
     * @return void
     */
    public function RENDER($arr = []) {

        if(!isset($arr['CODE'])) {
            $arr['CODE'] = http_response_code();
        }

        if(!isset($arr['ERROR'])) {
            $arr['ERROR'] = false;
        }

        if(isset($arr['TITLE'])) {
            $this->MTTR['TITLE'] = $arr['TITLE'] . $this->MTTR['SEPARATE'] . $this->APP['NAME'];
        } elseif(!isset($arr['TITLE']) && $arr['ERROR'] === true) {
            $this->MTTR['TITLE'] = 'Error' . $this->MTTR['SEPARATE'] . $this->APP['NAME'];
        }

        if(isset($arr['DESCRIPTION'])) {
            $this->MTTR['DESCRIPTION'] = $arr['DESCRIPTION'];
        }

        if(isset($arr['KEYWORDS'])) {
            $this->MTTR['KEYWORDS'] = $arr['KEYWORDS'];
        }

        if(!isset($arr['HEADER']) || !is_file($arr['HEADER'])) {
            $arr['HEADER'] = ROOT . $this->DIR['INCLUDES'] . 'header';
        }

        if(!isset($arr['FOOTER']) || !is_file($arr['FOOTER'])) {
            $arr['FOOTER'] = ROOT . $this->DIR['INCLUDES'] . 'footer';
        }

        http_response_code($arr['CODE']);

        /*
         * *********************************
         * *** Finally Rendering Payload ***
         * *********************************
         */

        /*
         * Calling Before Head Function
         */
        $this->CALL_FUNCS('BEFORE_HEAD');

        /*
         * Getting Body Content
         */
        $payload_body = $this->PAYLOAD;
        $this->PAYLOAD = '';

        if($arr['ERROR']) {

            /*
             * Error File
             */
            $file = str_replace($this->APP['BLADE_XT'], '', $this->HSA['ERROR_PATH']);

            if(array_key_exists($arr['CODE'], $this->APP['ERROR']) &&
                is_file(ROOT . $this->APP['ERROR'][$arr['CODE']] . $this->APP['BLADE_XT'])) {

                $file = ROOT . $this->APP['ERROR'][$arr['CODE']];
            }

            /*
             * Error Content
             */
            $this->BLADE('', [], ['location' => $file]);
            $payload_body = $this->PAYLOAD;
            $this->PAYLOAD = '';
        }

        /*
         * Getting Header Content
         */
        $this->BLADE('', [], ['location' => $arr['HEADER']]);
        $payload_header = $this->PAYLOAD;
        $this->PAYLOAD = '';

        /*
         * Getting Footer Content
         */
        $this->BLADE('', [], ['location' => $arr['FOOTER']]);
        $payload_footer = $this->PAYLOAD;
        $this->PAYLOAD = '';

        /*
         * Calling After Footer Function
         */
        $this->CALL_FUNCS('AFTER_FOOTER');

        /**
         * ******************************
         * *** PLAYLOAD OUTPUT BUFFER ***
         * ******************************
         */
        $output_payload = $this->TRIMS($payload_header . $payload_body . $payload_footer, " \/\r\n\t\n\r\\");
        eval(' ?>' . $output_payload . '<?php ');

        ob_end_flush();
        die();
    }
}

/*
 * ********************************
 * *** Setup & Run & Render App ***
 * ********************************
 *
 * ## For System Only ##
 */
$App = new App();
$App->RUN();
