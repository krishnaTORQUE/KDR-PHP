<?php

namespace KDO;

if (!defined('MAIN')) {
    require $_SERVER['ERROR_PATH'];
}

class App extends Sys {

    public static $_;

    private function App_() {
        self::$_ = $this;
    }

    private $ROUTE_MATCH = false;

    /*
     * **************************************
     * *** Setting Up Configs & Variables ***
     * **************************************
     */

    public function SETUP() {

        /*
         * Getting The ConFiguration
         */
        if (is_file(ROOT . '_config.php')) {
            require ROOT . '_config.php';
        }

        /*
         * Initialize The ConFiguration
         */
        switch ($this->APP['ENV']) {

            case 'develop':
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                break;

            case 'publish':
                error_reporting(0);
                ini_set('display_errors', 0);
                break;
        }

        $this->DIR['APP'] = '_' . $this->APP['ACTIVE'] . '/';
        $this->URI($this->APP['CASE']['URA']);
        $this->HEADERS($this->APP['CASE']['HSA']);

        ini_set('memory_limit', $this->APP['MEMORY_LIMIT']);

        if (strtolower($this->URA['PROTOCOL']) === 'https') {
            ini_set('session.cookie_secure', 1);
        }

        if ($this->APP['COOKIE_HTTP'] === true) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
        }

        if (strlen($this->APP['TIMEZONE']) > 1) {
            date_default_timezone_set($this->APP['TIMEZONE']);
        }

        if ($this->MAINTAIN()) {
            $this->RENDER([
                'code'  => 503,
                'ERROR' => true
            ]);
        }

        $this->APP_URL = $this->URA['APP'] . $this->DIR['APP'];
        $this->APP_PATH = ROOT . $this->DIR['APP'];

        /*
         * Making App Strings if exists
         */
        if (strlen($this->APP['STRINGS']) > 1) {
            require $this->APP_PATH . $this->APP['STRINGS'] . '.php';
            $strings = new \STRINGS($this);
            $this->STRINGS = $strings->DATA;
            unset($strings);
        }

        /*
         * Global App Access
         */
        $this->App_();

        /*
         * Getting Plugins & Autoloads
         */
        $this->PLUGS();

        /*
         * Calling the App Main Controller
         */
        $this->META['TITLE'] = $this->APP['NAME'];

        if (is_file($this->APP_PATH . 'controllers/MainController.php')) {
            require $this->APP_PATH . 'controllers/MainController.php';
        } else {
            $this->RENDER([
                'CODE'  => 503,
                'ERROR' => true
            ]);
        }

        /*
         * Error if no Route Match
         */
        if (!$this->ROUTE_MATCH) {
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

        if (is_dir($plug_path)) {

            $plugs_list = scandir($plug_path);

            foreach ($plugs_list as $plug) {

                if ($plug === '.' || $plug === '..') {
                    continue;
                }

                $plug_file = $plug_path . $plug;
                if (is_file($plug_file) && in_array($plug, $this->APP['PLUGS'])) {
                    require $plug_file;
                }
            }
        }
        unset($plug_path, $plugs_list, $plug, $plug_file);
    }

    /*
     * ************************
     * *** Add CSS, JS Tags ***
     * ************************
     * 
     * e.g. array_push($this->LOAD_TAGS['CSS'], 'URL');
     */

    public $LOAD_TAGS = [
        'ICON' => [
            'URL'  => '',
            'TYPE' => ''
        ],
        'CSS'  => [],
        'JS'   => []
    ];

    public function LOAD_TAGS($which, $str) {
        array_push($this->LOAD_TAGS[$which], $str);
    }

    /*
     * *********************
     * *** Add Functions ***
     * *********************
     * 
     * e.g. array_push($this->ADD_FUNC['IN_HEAD'], 'function_name');
     */

    public $ADD_FUNC = [
        'BEFORE_HEAD'  => [],
        'IN_HEAD'      => [],
        'IN_BODY'      => [],
        'IN_FOOTER'    => [],
        'AFTER_FOOTER' => [],
    ];

    public function ADD_FUNC($where, $func) {
        array_push($this->ADD_FUNC[$where], $func);
    }

    /*
     * ******************************
     * *** Calling Added Function ***
     * ******************************
     */

    protected function CALL_FUNCS($para) {
        if (array_key_exists($para, $this->ADD_FUNC)) {
            foreach ($this->ADD_FUNC[$para] as $func) {
                if (is_callable($func)) {
                    $func($this);
                } else {
                    echo $func;
                }
            }
        }
    }

    /*
     * ************************
     * *** Adding View File ***
     * ************************
     * 
     * (str)                $name       "Only Name of the file with is located in App `views` directory"
     * (bool)               $require    "If Exists > (true) Include/Require the path. (false) return path"
     * (array / strings)    $param      "Array or String\s can pass from App Controller to View File"
     */

    public function VIEW($name, $param = false) {
        $path = $this->APP_PATH . 'views/' . $name . '.php';
        $this->PARAM = $param;
        return (is_file($path)) ? $path : false;
    }

    /*
     * ***********************
     * *** Adding Any File ***
     * ***********************
     * 
     * (str)                $name       "Path of the file with is located in App"
     * (bool)               $require    "If Exists > (true) Include/Require the path. (false) return path"
     * (array / strings)    $param      "Array or String\s can pass from App Controller to the File"
     */

    public function FILE($name, $param = false) {
        $path = $this->APP_PATH . $name . '.php';
        $this->PARAM = $param;
        return (is_file($path)) ? $path : false;
    }

    /*
     * ***************************
     * *** Main Routing Method ***
     * ***************************
     * 
     * (str)    $arr['url']         "Request URL"
     *                              "Static URL          : /contact"
     *                              "Dynamic/Slug URL    : /user/{:usr_id:}"
     * 
     * (array)  $arr['METHOD']      "Methods in Array. Small Letters"
     * (array)  $arr['SCHEME']      "Schemes in Array. Small Letters"
     * (array)  $arr['FROM']        "Referer From Inbound as `in` / Outbound as `out`]"
     * (array)  $arr['FROM_HOST']   "Referer Host\s Name in Array"
     * (bool)   $arr['X-REQUEST']
     */

    public function ROUTE($arr = []) {

        if ($this->ROUTE_MATCH) {
            return false;
        }

        if (!isset($arr['FUNC'])) {
            $arr['FUNC'] = false;
        }

        if (!isset($arr['METHOD'])) {
            $arr['METHOD'] = $this->APP['REQUEST']['METHOD'];
        }

        if (!isset($arr['SCHEME'])) {
            $arr['SCHEME'] = $this->APP['REQUEST']['SCHEME'];
        }

        /*
         * Static & Dynamic / Slug URL match with given Route
         */
        $slug = [];
        $url = explode('/', $this->TRIMS($arr['URL']));
        $fpath = '';

        /*
         * Arrange URL with Route
         */
        for ($i = 0; $i < count($url); $i++) {

            if (!array_key_exists($i, $this->URA['PATHS'])) {
                break;
            }

            preg_match_all('@\{(.*?)\}@i', $url[$i], $url_match);

            /*
             * Match with Slug
             */
            if (array_key_exists(0, $url_match[0])) {
                $slug[$url_match[1][0]] = $this->URA['PATHS'][$i];
                $fpath .= '/' . $url_match[0][0];

                /*
                 * Match with String
                 */
            } elseif (strtolower($url[$i]) === strtolower($this->URA['PATHS'][$i])) {
                if ($this->APP['CASE']['URA'] === 'up') {
                    $this->URA['PATHS'][$i] = strtoupper($this->URA['PATHS'][$i]);
                } elseif ($this->APP['CASE']['URA'] === 'low') {
                    $this->URA['PATHS'][$i] = strtolower($this->URA['PATHS'][$i]);
                }
                $fpath .= '/' . $this->URA['PATHS'][$i];
            }
        }

        /*
         * URL Matched With Given Route
         */
        if ($arr['URL'] === $fpath && count($url) === count($this->URA['PATHS'])) {

            /*
             * App Strings Send to Client
             */
            if (strtoupper($arr['URL']) === '/--STRINGS--' &&
                isset($this->HSA['REFERER_FROM']) &&
                strtolower($this->HSA['REFERER_FROM']) === 'in' &&
                strtolower($this->HSA['METHOD']) === 'post') {

                $this->ROUTE_MATCH = true;
                if (isset($this->STRINGS)) {
                    echo json_encode($this->STRINGS, JSON_FORCE_OBJECT);
                }

                return false;
            }

            /*
             * Request From
             */
            if (isset($arr['FROM'])) {
                if (!isset($this->HSA['REFERER_FROM']) || !in_array($this->HSA['REFERER_FROM'], $arr['FROM'])) {
                    $this->RENDER([
                        'CODE'  => 403,
                        'ERROR' => true
                    ]);
                }
            } elseif (is_array($this->APP['REQUEST']['FROM'])) {
                if (!isset($this->HSA['REFERER_FROM']) || !in_array($this->HSA['REFERER_FROM'], $this->APP['REQUEST']['FROM'])) {
                    $this->RENDER([
                        'CODE'  => 403,
                        'ERROR' => true
                    ]);
                }
            }

            /*
             * Request From Host
             */
            if (isset($arr['FROM_HOST'])) {
                if (!isset($this->HSA['REFERER_HOST']) || !in_array($this->HSA['REFERER_HOST'], $arr['FROM_HOST'])) {
                    $this->RENDER([
                        'CODE'  => 403,
                        'ERROR' => true
                    ]);
                }
            } elseif (is_array($this->APP['REQUEST']['FROM_HOST'])) {
                if (!isset($this->HSA['REFERER_HOST']) || !in_array($this->HSA['REFERER_HOST'], $this->APP['REQUEST']['FROM_HOST'])) {
                    $this->RENDER([
                        'CODE'  => 403,
                        'ERROR' => true
                    ]);
                }
            }

            /*
             * Request Method
             */
            if (!in_array($this->HSA['METHOD'], $arr['METHOD'])) {
                $this->RENDER([
                    'CODE'  => 403,
                    'ERROR' => true
                ]);
            }

            /*
             * Request Schema
             */
            if (!in_array($this->HSA['SCHEME'], $arr['SCHEME'])) {
                $this->RENDER([
                    'CODE'  => 403,
                    'ERROR' => true
                ]);
            }

            /*
             * (bool) X-REQUEST
             */
            if (isset($arr['X-REQUEST']) && $arr['X-REQUEST'] !== $this->HSA['X-REQUEST']) {
                $this->RENDER([
                    'CODE'  => 403,
                    'ERROR' => true
                ]);
            }

            foreach ($slug as $key => $value) {

                /*
                 * Trim Slug URL
                 */
                if (isset($arr['TRIM']) && is_array($arr['TRIM']) && array_key_exists($key, $arr['TRIM'])) {
                    $slug[$key] = preg_replace($arr['TRIM'][$key], '', $value);
                }

                /*
                 * Match Slug URL
                 */
                if (isset($arr['MATCH']) && is_array($arr['MATCH']) && array_key_exists($key, $arr['MATCH'])) {

                    if (preg_match_all($arr['MATCH'][$key], $value)) {
                        continue;
                    } else {
                        $slug = 'wrong';
                        break;
                    }
                }
            }

            if ($slug === 'wrong') {
                return false;
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
     * 
     * Route Callback to the Function \ Class
     */

    private function ROUTE_CALLBACK($cb, $pass_arg = false) {
        if (is_string($cb)) {

            $obj = explode('+', $cb);

            require $this->APP_PATH . 'controllers/' . $obj[0] . '.php';
            $call = new $obj[0]($this, $pass_arg);

            if (array_key_exists(1, $obj)) {
                $method = $obj[1];
                $call->$method($this, $pass_arg);
            }
        } elseif (is_callable($cb)) {
            $cb($this, $pass_arg);
        } else {
            $this->RENDER([
                'CODE'  => 404,
                'ERROR' => true
            ]);
        }
    }

    /*
     * ***********************
     * *** Final Rendering ***
     * ***********************
     * 
     * (str / array)    $arr['FILE']        "Path To File"
     *                                      "Single File    : $arr['FILE'] = 'path/to/file'"
     *                                      "Many Files     : $arr['FILE'] = [
     *                                                                          'path/to/file-1',
     *                                                                          'path/to/file-2',
     *                                                                      ]
     * (int)            $arr['CODE']        "Response Code"
     * (bool            $arr['ERROR']
     * (str)            $arr['TITLE']       "Meta Tag Title"
     * (str)            $arr['DESCRIPTION'] "Meta Tag Description"
     * (str)            $arr['KEYWORDS']    "Meta Tag Keywords"
     */

    public function RENDER($arr = []) {

        if (!isset($arr['CODE'])) {
            $arr['CODE'] = 200;
        }
        if (!isset($arr['ERROR'])) {
            $arr['ERROR'] = false;
        }
        if ($arr['ERROR'] && !isset($arr['FILE'])) {
            $arr['FILE'] = ROOT . $this->APP['ERROR'][$arr['CODE']];
        }

        if (isset($arr['TITLE'])) {
            $this->META['TITLE'] = $arr['TITLE'] . $this->META['SEPARATE'] . $this->APP['NAME'];
        } elseif (!isset($arr['TITLE']) && $arr['ERROR'] === true) {
            $this->META['TITLE'] = 'Error' . $this->META['SEPARATE'] . $this->APP['NAME'];
        }

        if (isset($arr['DESCRIPTION'])) {
            $this->META['DESCRIPTION'] = $arr['DESCRIPTION'];
        }

        if (isset($arr['KEYWORDS'])) {
            $this->META['KEYWORDS'] = $arr['KEYWORDS'];
        }

        if (!isset($arr['HEADER'])) {
            $arr['HEADER'] = ROOT . $this->DIR['INCLUDES'] . 'header.php';
        }

        if (!isset($arr['FOOTER'])) {
            $arr['FOOTER'] = ROOT . $this->DIR['INCLUDES'] . 'footer.php';
        }

        http_response_code($arr['CODE']);

        /*
         * Finally Rendering Payload
         */

        if ($arr['HEADER'] && is_file($arr['HEADER'])) {
            require $arr['HEADER'];
        }

        if (is_array($arr['FILE'])) {
            foreach ($arr['FILE'] as $file) {
                if (is_file($file)) {
                    require $file;
                }
            }
        } elseif (is_file($arr['FILE'])) {
            require $arr['FILE'];
        } else {
            require $_SERVER['ERROR_PATH'];
        }

        if ($arr['FOOTER'] && is_file($arr['FOOTER'])) {
            require $arr['FOOTER'];
        }

        die();
    }

}

/*
 * ************************************
 * *** Setup & Run App & Render ***
 * ************************************
 */
$App = new App();
$App->SETUP();
