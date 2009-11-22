<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package request
 *
 * This dispatcher stores a mapping of requests to handlers and dispatches requests to their correct handler
 */
class Dispatcher {
    private static $listeners = array();

    /**
     * This method takes the given request finds the request handler and passes the request to the handler
     *
     * @param Event $e
     * @return unknown
     */
    public static function dispatch(Request $r) {

        if (array_key_exists($r->getName(), self::$listeners)) {
            self::$listeners[$r->getName()]->execute($r);
        }
        else {
            throw new UnknownRequest("No handler for ".$r->getName());
        }
    }

    /**
     * Ok this registers a method to call for a given a request
     *
     * @param String $request
     * @param String $class
     * @param String $method
     * @param int $cacheLength
     * @param array $parameterMap
	 */
    public static function addListener($requestName, $class, $method, $cacheLength = false, array $parameterMap = array(), $authenticator = null) {
        self::$listeners[$requestName] = new Resource($requestName,
                                                      $class,
                                                      $method,
                                                      $parameterMap,
                                                      $authenticator,
                                                      $cacheLength);
    }

    public static function addResource(Resource $resource) {
        self::$listeners[$resource->getName()] = $resource;
    }

    /**
     * get the cache length for the given request
     *
     * @param $request Request to get the cache length for
     */
    public static function getCacheLength(Request $r) {
        if (array_key_exists($r->getName(), self::$listeners)) {
            return self::$listeners[$r->getName()]->getCacheLength();
        }
        else {
           return false;
        }
    }

    /**
     * Get the parameter map for the given request
     * @param array $requestName
     * @return array
     */
    public static function getParameterMap($requestName) {
        return array_key_exists($requestName,self::$listeners) ? self::$listeners[$requestName]->getParameterMap() : array();
    }

    /**
     * return array
     */
    public static function getListeners() {
        return self::$listeners;
    }
}