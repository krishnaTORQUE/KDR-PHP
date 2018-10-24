<?php

namespace KDO;

if (!defined('MAIN')) {
    require $_SERVER['ERROR_PATH'];
}

class Sys {

    /*
     * *******************
     * *** App Details ***
     * *******************
     */
    public $APP = [

        /*
         * ************************
         * *** Platform Details ***
         * ************************
         */
        'PLATFORM'     => [
            'NAME'    => 'KDO',
            'VERSION' => '1.1',
            'STATUS'  => 'Alpha'
        ],

        /*
         * App Name
         */
        'NAME'         => 'KDO App',

        /*
         * Charset
         */
        'CHARSET'      => 'UTF-8',

        /*
         * App Time Zone
         */
        'TIMEZONE'     => '',

        /*
         * Active Template Directory Name
         */
        'ACTIVE'       => 'default',

        /*
         * App Mode
         * [run, maintain]
         */
        'MODE'         => 'run',

        /*
         * App Environment
         * [develop, publish]
         * Server Default If not set
         */
        'ENV'          => 'develop',

        /*
         * Static Strings Class File Path
         */
        'STRINGS'      => '',

        /*
         * Active Plugings & Autoloads
         */
        'PLUGS'        => [],

        /*
         * Minify Payload
         */
        'MIN_PAYLOAD'  => false,

        /*
         * Method Type
         * Schema Type
         * Referer From (Inbound, Outbound)
         * Referer From Host\s
         */
        'REQUEST'      => [

            /*
             * Request Method 
             */
            'METHOD'    => ['get', 'post'],

            /*
             * Accept Request Schema
             * Set to `any` for All Schemas
             */
            'SCHEME'    => ['http', 'https'],

            /*
             * Request From
             * [in, out]
             */
            'FROM'      => '',

            /*
             * List of Request From Hosts
             * Add App host too
             */
            'FROM_HOST' => ''
        ],

        /*
         * Dynamic Error Page
         * Files Path
         */
        'ERROR'        => [
            400 => '__KDO/Includes/error.php',
            403 => '__KDO/Includes/error.php',
            404 => '__KDO/Includes/error.php',
            503 => '__KDO/Includes/error.php'
        ],

        /*
         * Cookie HTTP Only
         */
        'COOKIE_HTTP'  => true,

        /*
         * Set Memory Limit
         */
        'MEMORY_LIMIT' => '128M',

        /*
         * Other Options
         */
        'OPTIONS'      => [

            /*
             * URA Values Case In\Sensitive
             */
            'URA_CASE'      => false,

            /*
             * HSA Values Case In\Sensitive
             */
            'HSA_CASE'      => 'low',

            /*
             * Default Query String Allow / Disallow
             */
            'QUERY_STR'     => false,

            /*
             * Allow / Disallow Empty/Bad User Agent
             */
            'BAD_USERAGENT' => false
        ]
    ];

    /*
     * *********************
     * *** Set Meta Tags ***
     * *********************
     */
    public $META = [
        'SEPARATE'     => ' - ',
        'DESCRIPTION'  => '',
        'KEYWORDS'     => '',
        'CONTENT_TYPE' => 'text/html'
    ];

    /*
     * *********************************
     * *** Set HTML Tag's Attributes ***
     * *********************************
     */
    public $TAG_ATTR = [
        'DOCTYPE' => '',
        'HTML'    => ' lang="en"',
        'HEAD'    => '',
        'BODY'    => ''
    ];

    /*
     * ********************
     * *** Global Array ***
     * ********************
     */
    public $GLOB = [];

    /*
     * *************************
     * *** Directories Paths ***
     * *************************
     */
    public $DIR = [
        'KDO'      => '__KDO/',
        'INCLUDES' => '__KDO/Includes/',
        'PLUGS'    => '__PLUGS/',
        'TMP'      => '__TMP/'
    ];

    /*
     * ***************************
     * *** Get Directory Paths ***
     * ***************************
     * 
     * (str) $name      "Name of the Directory"
     * (str) $sub       "path of the directory inside of the main directory `$name`"
     * 
     * If $name = TMP   "If TMP directory is not exists it will create & it will create sub-directory as well"
     */
    public function DIR($name, $sub = null) {

        $path = false;
        if (array_key_exists($name, $this->DIR)) {

            if ($name === 'TMP' && !is_dir(ROOT . $this->DIR['TMP'])) {
                mkdir(ROOT . $this->DIR['TMP'], 0777, TRUE);
            }

            $path = $this->DIR[$name];

            if (isset($sub)) {

                $path = $this->DIR[$name] . $sub . '/';

                if ($name === 'TMP' && !is_dir(ROOT . $path)) {
                    mkdir(ROOT . $path, 0777, TRUE);
                } elseif ($name !== 'TMP' && !is_dir($path)) {
                    $path = false;
                }
            }
        }
        return $path;
    }

    /*
     * ************************************
     * *** Filtered Parse URL/URI Array ***
     * ************************************
     * 
     * $case = up, low    "up as Upper Case | low as Lower Case. false to do nothing"
     * $dash to convert _ > - or - > _
     * 
     * Calling this function will reload $this->URA Values
     */
    public function URI($case = false, $dash = false) {

        $this->URA = [];

        if (!isset($this->URA['PROTOCOL'])) {

            if ((array_key_exists('HTTPS', $_SERVER) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) ||
                (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {

                $this->URA['PROTOCOL'] = 'https';
            } else {
                $this->URA['PROTOCOL'] = 'http';
            }
        }

        if ($case === 'up') {
            $this->URA['PROTOCOL'] = strtoupper($this->URA['PROTOCOL']);
        } elseif ($case === 'low') {
            $this->URA['PROTOCOL'] = strtolower($this->URA['PROTOCOL']);
        }

        $this->URA['HOST'] = $_SERVER['HTTP_HOST'];
        $this->URA['URI'] = $_SERVER['REQUEST_URI'];

        $full = $this->URA['PROTOCOL'] . '://' . $this->URA['HOST'] . $this->URA['URI'];

        if ($case === 'up') {
            $full = strtoupper($full);
        } elseif ($case === 'low') {
            $full = strtolower($full);
        }

        if ($dash === 'dash') {
            $full = preg_replace('@_@i', '-', $full);
        } elseif ($dash === 'und') {
            $full = preg_replace('@\-@i', '_', $full);
        }

        $full = $this->TRIMS($full);
        $full = htmlspecialchars($full, ENT_QUOTES, $this->APP['CHARSET']);

        $fpath = parse_url($full, PHP_URL_PATH);
        $fpath = substr($fpath, strlen(PATH));

        $query = parse_url($full, PHP_URL_QUERY);
        $qstre = explode('&amp;', $query);
        $QUERIES = [];

        for ($i = 0; $i < count($qstre); $i++) {
            $qstre_e = explode('=', $qstre[$i]);
            if (isset($qstre_e[0], $qstre_e[1])) {
                $QUERIES[$qstre_e[0]] = $qstre_e[1];
            }
        }

        $this->URA['APP'] = $this->URA['PROTOCOL'] . '://' . $this->URA['HOST'] . PATH;
        $this->URA['FULL'] = $full . '/';
        $this->URA['FPATH'] = '/' . $fpath;
        $this->URA['PATHS'] = explode('/', $fpath);
        $this->URA['FPATHQ'] = $this->URA['FPATH'] . '?' . $query;
        $this->URA['QUERY'] = $query;
        $this->URA['QUERIES'] = $QUERIES;
    }

    /*
     * *****************************
     * *** Header & Server Array ***
     * *****************************
     * 
     * $case = up, low    "up as Upper Case | low as Lower Case."
     * Calling this function will reload $this->HSA Values
     */
    public function SERVER($case = false) {

        $this->HSA = $_SERVER;

        /*
         * Http Response Code
         */
        $this->HSA['RESPONSE'] = (array_key_exists('RESPONSE_CODE', $this->HSA) ? $this->HSA['RESPONSE_CODE'] : http_response_code());

        /*
         * User Agent & Case
         */
        $this->HSA['USERAGENT'] = $this->HSA['HTTP_USER_AGENT'];
        if ($case === 'up') {
            $this->HSA['USERAGENT'] = strtoupper($this->HSA['USERAGENT']);
        } elseif ($case === 'low') {
            $this->HSA['USERAGENT'] = strtolower($this->HSA['USERAGENT']);
        }

        /*
         * Method & Case
         */
        $this->HSA['METHOD'] = $this->HSA['REQUEST_METHOD'];
        if ($case === 'up') {
            $this->HSA['METHOD'] = strtoupper($this->HSA['METHOD']);
        } elseif ($case === 'low') {
            $this->HSA['METHOD'] = strtolower($this->HSA['METHOD']);
        }

        /*
         * Scheme & Case
         */
        $this->HSA['SCHEME'] = $this->HSA['REQUEST_SCHEME'];
        if ($case === 'up') {
            $this->HSA['SCHEME'] = strtoupper($this->HSA['SCHEME']);
        } elseif ($case === 'low') {
            $this->HSA['SCHEME'] = strtolower($this->HSA['SCHEME']);
        }

        /*
         * X Request Check
         */
        $this->HSA['X-REQUEST'] = (array_key_exists('HTTP_X_REQUESTED_WITH', $this->HSA) || array_key_exists('X-Requested-With', $this->HSA));

        /*
         * Referer Check & Details
         */
        if (array_key_exists('HTTP_REFERER', $this->HSA) &&
            !is_null($this->HSA['HTTP_REFERER']) &&
            strlen($this->HSA['HTTP_REFERER']) > 1) {

            /*
             * Referer Link & Case
             */
            $this->HSA['REFERER_LINK'] = $this->HSA['HTTP_REFERER'];
            if ($case === 'up') {
                $this->HSA['REFERER_LINK'] = strtoupper($this->HSA['REFERER_LINK']);
            } elseif ($case === 'low') {
                $this->HSA['REFERER_LINK'] = strtolower($this->HSA['REFERER_LINK']);
            }

            /*
             * Referer Host
             */
            $this->HSA['REFERER_PARSE_URL'] = parse_url($this->HSA['REFERER_LINK']);
            $this->HSA['REFERER_HOST'] = $this->HSA['REFERER_PARSE_URL']['host'];

            $pattern = '@' . $this->HSA['REFERER_HOST'] . '@i';
            $subject = $this->URA['APP'];

            /*
             * Check Referer From
             */
            if (preg_match($pattern, $subject)) {
                $this->HSA['REFERER_FROM'] = 'in';
            } else {
                $this->HSA['REFERER_FROM'] = 'out';
            }

            if ($case === 'up') {
                $this->HSA['REFERER_FROM'] = strtoupper($this->HSA['REFERER_FROM']);
            }
        }
    }

    /*
     * Get Maintain Mode
     * [Return Bool]
     */
    public function MAINTAIN() {
        return ($this->APP['MODE'] === 'maintain' || is_file(ROOT . '.maintain'));
    }

    /*
     * Home Page
     * [Return Bool]
     */
    public function HOME() {
        if ($this->URA['APP'] === $this->URA['FULL'] ||
            $this->URA['FPATH'] === '/' ||
            strlen($this->URA['FPATH']) < 2 ||
            (array_key_exists(0, $this->URA['PATHS']) && strlen($this->URA['PATHS'][0]) < 1)) {

            return true;
        }
        return false;
    }

    protected function TRIMS($content, $white = null, $delmi = " ,\/\t\n\r\\") {
        $content = trim($content, $delmi);
        $content = ltrim($content, $delmi);
        $content = rtrim($content, $delmi);

        if (isset($white)) {
            $content = preg_replace('/\s+/', $white, $content);
        }
        return $content;
    }

}