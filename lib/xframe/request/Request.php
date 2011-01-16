<?php

namespace xframe\request;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package request
 *
 * This encapsulates a given request. Usually this object will be routed
 * through the front controller and handled by a request controller
 */
class Request {
    private $requestedResource;
    private $parameters;
    private $mappedParameters;

    public $files;
    public $cookies;
    
    /**
     *
     * @param string $requestURI
     * @param array $parameters
     * @param string $phpSelf
     */
    public function __construct($requestURI,
                                array $parameters = array(),
                                $phpSelf = "/index.php") {

        //get the base directory
        $baseDirectory = substr($phpSelf, 0, -10);
        //remove from the URI
        $request = str_replace($baseDirectory, "", $requestURI);
        //see if there is a query attached
        $end = strpos($request, "?");
        //remove the query from the request
        $request = $end === false ? substr($request, 1) : substr($request, 1, $end - 1);
        //check for blank request
        $request = ($request == '') ? 'index' : $request;
        //support for urls with request/param/param
        $request = explode("/", $request);
        //get the request name
        $this->requestedResource = $request[0];
        //get the parameters out of the request URI
        $this->mappedParameters = array_slice($request, 1);
        //store the other params (usually from $_REQUEST)
        $this->parameters = $parameters;
        //$this->getParamsFromRawHTTPRequest();
        //get the $_FILES array
        $this->files = $_FILES;
        $this->cookie = $_COOKIE;
    }

    /**
     * Apply the given parameter map to this request.
     *
     * loop over the parameters from the request URI and inject them into the
     * parameters given in the constructor (usually these come from $_REQUEST)
     *
     * @param array $map
     */
    public function applyParameterMap(array $map) {
        foreach ($this->mappedParameters as $paramNum => $value) {
            $this->parameters[$map[$paramNum]] = $value;
        }
    }

    /**
     * @return string
     */
    public function getRequestedResource() {
        return $this->requestedResource;
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
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
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
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
        $this->parameters[$key] = $value;
    }

    /**
     * Unset the given variable
     * @param mixed $key
     */
    public function __unset($key) {
        unset($this->parameters[$key]);
    }

    /**
     * @param $key
     * @return boolean
     */
    public function __isset($key) {
        return isset($this->parameters[$key]);
    }

    /**
     * Return a hash of the Request
     */
    public function hash() {
        return md5($this->requestedResource.
                   implode($this->parameters).
                   implode(array_keys($this->parameters)));
    }

    /**
     * issue with POST variable in PHP not be properly populated in some circumstances:
     * http://getluky.net/2009/02/24/php-_post-array-empty-although-phpinput-and-raw-post-data-is-available/
     */
    private function getParamsFromRawHTTPRequest() {
        $keyValuePairs = explode("&", file_get_contents('php://input'));

        foreach ($keyValuePairs as $keyValue) {
            $data = explode("=", $keyValue);
            $this->parameters[$data[0]] = $data[1];
        }
    }

}