<?php

if(!defined('ROOT')) require $_SERVER['ERROR_PATH'];

class Sys {

    public $URA;
    public $HSA;
    public $AFA = [
        'ROOT' => ROOT,
        'PATH' => PATH
    ];

    /*
     * ***************************
     *        Details OF
     *  App, Platform, Config etc
     * ***************************
     */
    public $APP = [

        /*
         * Platform Details
         * ## Read Only ##
         */
        'PLATFORM'     => [
            'NAME'    => 'KDR',
            'VERSION' => '1.5',
            'STATUS'  => 'Stable'
        ],

        /*
         * App Name
         */
        'NAME'         => 'KDR',

        /*
         * App Charset
         */
        'CHARSET'      => 'UTF-8',

        /*
         * Set Memory Limit
         */
        'MEMORY_LIMIT' => '128M',

        /*
         * App TimeZone
         */
        'TIMEZONE'     => false,

        /*
         * Active App Directory Name
         */
        'ACTIVE'       => 'default',

        /*
         * Blade System
         * File Extension
         */
        'BLADE_XT'     => '.php',

        /*
         * Static Strings Class File Path
         */
        'STRINGS'      => false,

        /**
         * Minify Payload
         *
         * @value bool   Minify Client Side Payload
         */
        'MIN_PAYLOAD'  => false,

        /**
         * App Mode
         *
         * @value bool   Active maintain mode
         */
        'MAINTAIN'     => false,

        /**
         * App Environment
         *
         * @value bool    Switch to Publish / Production
         */
        'PUBLISH'      => false,

        /**
         * Active Plugins & Autoloads
         *
         * @value array     e.g. ['file1', 'file2']
         */
        'PLUGS'        => [],

        /*
         * Request & Referer
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

            /**
             * Request From
             *
             * @value string    Options (in, out, '')
             *                  in          > Request only from inbound
             *                  out         > Request only from outbound
             *                  ''|blank    > Request from any
             */
            'FROM'      => '',

            /**
             * List of Request From Hosts
             *
             * @value array     ['host1', 'host2']
             *                      Domain Name
             *                  include self host too
             */
            'FROM_HOST' => '',

            /**
             * Query String
             *
             * @value bool
             *        true    for allow
             *        false   for deny
             */
            'QUERY_STR' => false,

            /**
             * X Request
             *
             * @value bool
             *        true    for allow
             *        false   for deny
             */
            'X_REQUEST' => false
        ],

        /*
         * Security Options
         */
        'SECURITY'     => [

            /*
             * Cookie HTTP Only
             */
            'COOKIE_HTTP'     => true,

            /**
             * Maximum URL Characters Length
             * Modern Browser:  2083
             * Default:         1000
             *
             * @value int       Limit Number. @False for no limit
             *                                  (Browser Default)
             */
            'MAX_URI_CHAR'    => 1000,

            /**
             * Block Bad Agent
             *
             * @value bool          @True. Render Error If bad Agent
             */
            'BAD_AGENT_BLOCK' => false,

            /**
             * Bad Agent
             * ## Read Only ##
             *
             * @return bool         If bad Agent founded
             */
            'BAD_AGENT'       => false
        ],

        /**
         * Dynamic Error Page
         * Files Path
         *
         * @key   int       Response Code
         * @value string    File Path
         *
         * e.g.             404 => 'path/to/404',
         *                     (No Extension)
         */
        'ERROR'        => []
    ];

    /*
     * ********************
     * *** Global Array ***
     * ********************
     */
    public $GLOB = [];

    /*
     * ******************************
     * *** HTML Tags & Attributes ***
     * ******************************
     */
    public $MTTR = [
        'DOCTYPE'     => '',
        'HTML'        => ' lang="en"',
        'HEAD'        => '',
        'BODY'        => '',
        'SEPARATE'    => ' - ',
        'DESCRIPTION' => '',
        'KEYWORDS'    => '',
        'TYPE'        => 'text/html'
    ];

    /*
     * ***********************
     * *** Directory Paths ***
     * ***********************
     */
    public $DIR = [
        'SYSTEM'   => '__KDR/',
        'INCLUDES' => '__KDR/Includes/',
        'PLUGS'    => '__PLUGS/',
        'TMP'      => '__TMP/'
    ];

    /**
     * ***************************
     * *** Get Directory Paths ***
     * ***************************
     *
     * @param string $name Name of the Directory
     * @param string $sub  path of the directory inside of the main directory
     *
     * NOTE:
     * If $name = TMP
     *
     *    It will create & it will create sub-directory as well,
     *    if TMP directory is not exists
     *
     * @return string
     */
    public function DIRf($name, $sub = false) {

        $path = false;
        if(array_key_exists($name, $this->DIR)) {

            if($name === 'TMP' && !is_dir(ROOT . $this->DIR['TMP'])) {
                mkdir(ROOT . $this->DIR['TMP'], 0777, true);
            }

            $path = $this->DIR[$name];

            if($sub) {

                $path .= $sub . '/';

                if($name === 'TMP' && !is_dir(ROOT . $path)) {
                    mkdir(ROOT . $path, 0777, true);
                } elseif($name !== 'TMP' && !is_dir($path)) {
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
     */
    protected function URI() {

        $this->URA = [];

        $this->URA['PROTOCOL'] = 'http';
        if((array_key_exists('HTTPS', $_SERVER) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) ||
            (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {

            $this->URA['PROTOCOL'] = 'https';
        }

        $this->URA['HOST'] = $_SERVER['HTTP_HOST'];
        $this->URA['URI'] = $_SERVER['REQUEST_URI'];

        $full = $this->URA['PROTOCOL'] . '://' . $this->URA['HOST'] . $this->URA['URI'];
        $full = $this->TRIMS($full);
        $full = htmlspecialchars($full, ENT_QUOTES, $this->APP['CHARSET']);

        $fpath = parse_url($full, PHP_URL_PATH);
        $fpath = substr($fpath, strlen(PATH));

        $query = parse_url($full, PHP_URL_QUERY);
        $qstre = explode('&amp;', $query);
        $QUERIES = [];

        for($i = 0; $i < count($qstre); $i++) {
            $qstre_e = explode('=', $qstre[$i]);
            if(isset($qstre_e[0], $qstre_e[1])) {
                $QUERIES[$qstre_e[0]] = $qstre_e[1];
            }
        }

        $this->URA['APP'] = $this->URA['PROTOCOL'] . '://' . $this->URA['HOST'] . PATH;
        $this->URA['FULL'] = $full;
        $this->URA['FPATH'] = '/' . $fpath;
        $this->URA['PATHS'] = explode('/', $fpath);
        $this->URA['FPATHQ'] = $this->URA['FPATH'] . '?' . $query;
        $this->URA['QUERY'] = $query;
        $this->URA['QUERIES'] = $QUERIES;

        unset($full, $fpath, $query, $qstre, $QUERIES);
    }

    /*
     * *****************************
     * *** Header & Server Array ***
     * *****************************
     */
    protected function SERVER() {

        $this->HSA = $_SERVER;

        /*
         * Http Response Code
         */
        $this->HSA['RESPONSE'] = (array_key_exists('RESPONSE_CODE', $this->HSA) && $this->HSA['RESPONSE_CODE'] > 99) ?
            $this->HSA['RESPONSE_CODE'] : http_response_code();

        /*
         * User Agent & Case
         */
        $this->HSA['USERAGENT'] = $this->TRIMS($this->HSA['HTTP_USER_AGENT']);

        /*
         * Method & Case
         */
        $this->HSA['METHOD'] = $this->TRIMS($this->HSA['REQUEST_METHOD']);

        /*
         * Scheme & Case
         */
        $this->HSA['SCHEME'] = $this->HSA['REQUEST_SCHEME'];

        /*
         * X Request Check
         */
        $this->HSA['X_REQUEST'] = (array_key_exists('HTTP_X_REQUESTED_WITH',
                $this->HSA) || array_key_exists('X-Requested-With', $this->HSA));

        /*
         * Referer Check & Details
         */
        if(array_key_exists('HTTP_REFERER', $this->HSA) &&
            !is_null($this->HSA['HTTP_REFERER']) &&
            strlen($this->HSA['HTTP_REFERER']) > 1) {

            /*
             * Referer Link & Case
             */
            $this->HSA['REFERER_LINK'] = $this->HSA['HTTP_REFERER'];

            /*
             * Referer Host
             */
            $this->HSA['REFERER_PARSE_URL'] = parse_url($this->HSA['REFERER_LINK']);
            $this->HSA['REFERER_HOST'] = $this->HSA['REFERER_PARSE_URL']['host'];

            /*
             * Check Referer From
             */
            $pattern = '@' . $this->HSA['REFERER_HOST'] . '@i';
            if(preg_match($pattern, $this->URA['APP'])) {
                $this->HSA['REFERER_FROM'] = 'in';
            } else {
                $this->HSA['REFERER_FROM'] = 'out';
            }
        }
    }

    /**
     * Home Page
     *
     * @return bool
     */
    public function HOME() {
        if($this->URA['APP'] === $this->URA['FULL'] ||
            $this->URA['FPATH'] === '/' ||
            strlen($this->URA['FPATH']) < 2 ||
            (array_key_exists(0, $this->URA['PATHS']) && strlen($this->URA['PATHS'][0]) < 1)) {

            return true;
        }
        return false;
    }

    /**
     * Get Maintain Mode
     *
     * @return bool
     */
    public function MAINTAIN() {
        return ($this->APP['MAINTAIN'] || is_file(ROOT . '.maintain'));
    }

    /*
     * Trim Content
     * ## For System Only ##
     */
    protected function TRIMS($content, $delmi = " ,\/\r\n\t\n\r\\") {
        $content = trim($content, $delmi);
        $content = ltrim($content, $delmi);
        $content = rtrim($content, $delmi);
        return $content;
    }
}
