<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package request
 *
 * A request encapsulates a given request
 */
class Request implements ArrayAccess, XML {
    private $params = array();
    private $name;

    /**
     * The request to be passed to the dispatcher. The $name is used to get the
     * type of request and the argArray is all the properties you want the request
     * to have so if your updating a product for example this could be the
     * $_POST variable containing all the new lovely product values.
     *
     * @param String $name
     * @param array $argArray
     */
    public function __construct($name, array $argArray = array()) {
        $this->name = $name;
        $this->params = $argArray;
        unset($this->params["PHPSESSID"]);
    }

    /**
     * Process the current page request
     */
    public static function process() {
        //generate the request
        $request = self::buildRequest();
        $page = false;

        //setup options
        $debugMode = array_key_exists("debug",$_GET) && $_GET["debug"] == "true";
        $xmlOutput = array_key_exists("debug",$_GET) && $_GET["debug"] == "xml";
        $cacheOn = array_key_exists("cache",$_GET) && $_GET["cache"] != "no";
        $cacheOn = Dispatcher::getCacheLength($request) != false && $cacheOn && Registry::get("CACHE_ENABLED");
        $debugEnabled = Registry::get("DEBUG_ENABLED");

        //check to see if we can get the cache version
        if ($cacheOn) {
            $page = Cache::mch()->get($request->hash());
        }

        //if the page wasnt in the cache or the cache is off
        if ($page === false) {
            //set page options
            if ($debugMode && $debugEnabled) {
                Page::setOutputMode(Page::OUTPUT_DEBUG);
            }
            else if ($xmlOutput && $debugEnabled) {
                Page::setOutputMode(Page::OUTPUT_XML);
            }

            try {
                //dispatch the request and build the page
                $request->dispatch();
                //transform the page and get the html
                $page = Page::build();

                //store the request response if possible
                if ($cacheOn) {
                    Cache::mch()->set($request->hash(), $page, false, $cacheLength);
                }
            }
            catch (FrameEx $ex) {
                //this exception can be UnknownRequest MalformedPage or just an uncaught FrameEx
                $ex->output();
                //replace the xslt with the standard errors.xsl and display the page
                $page = Page::displayErrors();
            }
        }

        //output the page
        echo $page;
    }

    /**
     * Create a request from the current page request
     */
    private static function buildRequest() {
        //take of the index.php so we can work out the sub folder
        $path = substr($_SERVER["PHP_SELF"], 0, -9);
        //remove the subfolders and query from the request
        if ($path == "/") {
            $request = substr(str_replace("?".$_SERVER["QUERY_STRING"], "", $_SERVER["REQUEST_URI"]), 1);
        } else {
            $request = str_replace(array($path, "?".$_SERVER["QUERY_STRING"]), "", $_SERVER["REQUEST_URI"]);
        }
        //check for blank request
        $request = ($request == '' || $request == '/') ? 'home' : $request;
        //support for urls with request/param/param
        $request = explode("/", $request);
        //get the request name
        $requestResource = $request[0];
        //everything else is param so get the param and map params to names
        $pm = Dispatcher::getParameterMap($requestResource);
        $request = array_slice($request, 1);
        $mappedRequest = array();
        $numParams = count($request);

        for($i = 0; $i < $numParams; $i++) {
            $mappedRequest[$pm[$i]] = $request[$i];
        }

        $request = array_merge($_REQUEST, $mappedRequest);

        return new Request($requestResource, $request);
    }

    /**
     * Magic function overload. If a variable on this object is accessed but
     * it doesnt exist try get it from the params array. This means that you
     * can now give an array like $_POST or $_GET in the constructor and then
     * access the fields like $e->myVar etc. Enjoy.
     *
     * @param mixed $key
     * @return mixed
     */
    public function __get($key) {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
    }

    /**
     * Magic function overload. If you try to set a variable that doesnt exist
     * this function is called. So setting $e->face = "your" when the variable
     * face doesn't exists sets it in the interal array for later access.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->params[$key] = $value;
    }

    /**
     * Unset the given variable
     * @param mixed $key
     */
    public function __unset($key) {
        unset($this->params[$key]);
    }

    /**
     * @param $key
     * @return boolean
     */
    public function __isset($key) {
        return isset($this->params[$key]);
    }

    /**
     * Returns the name of the request
     *
     * @return String
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name of the request
     *
     * @param String $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns the params of the request
     *
     * @return String
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Dispatches the request using Dispatcher::dispatch
     */
    public function dispatch() {
        return Dispatcher::dispatch($this);
    }

    /**
     * Return a hash of the Request
     */
    public function hash() {
        return md5($this->name.implode($this->params).implode(array_keys($this->params)));
    }

    /**
     * @return string xml
     */
    public function getXML() {
        $xml = "<request name='{$this->name}'>";
        foreach ($this->params as $key => $value) {
           $xml .= "<parameter name='{$key}'>{$value}</parameter>";
        }
        $xml .= "</request>";

        return $xml;
    }
    ////////////////////////////////////////////////////////////////////
    // ArrayAccess implementation
    ////////////////////////////////////////////////////////////////////

    /**
     * check to see whether an array key exists
     *
     * @param $key string array key to check
     */
    public function offsetExists($key) {
        return array_key_exists($key, $this->params);
    }

    /**
     * return a value
     *
     * @param $key string value to return
     */
    public function offsetGet($key) {
        return $this->params[$key];
    }

    /**
     * set a value
     *
     * @param $key string key of the value to set
     * @param $value mixed value to set
     */
    public function offsetSet($key, $value) {
        return $this->params[$key] = $value;
    }

    /**
     * unset a value from the array
     *
     * @param $key string key to unset
     */
    public function offsetUnset($key) {
        unset($this->params[$key]);
    }
}