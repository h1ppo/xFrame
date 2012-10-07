<?php

namespace xframe\request;

use \xframe\core\DependencyInjectionContainer;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package request
 *
 * This encapsulates a given request. Usually this object will be routed
 * through the front controller and handled by a request controller
 */
class FrontController {

    /**
     * Stores the root directory and provides access to the database handle
     * @var DependencyInjectionContainer
     */
    private $dic;

    /**
     * Default handler for 404 requests
     * @var Controller
     */
    private $notFoundController;

    /**
     * Setup the initial state
     * @param DependencyInjectionContainer $dic
     */
    public function __construct(DependencyInjectionContainer $dic,
                                Controller $notFoundController = null) {
        $this->dic = $dic;
        $this->notFoundController = $notFoundController;
    }
    
    /**
     * Dispatches the given request to it's controller
     * @param Request $request 
     */
    public function dispatch(Request $request) {

        $controller = $this->loadResource($request);

        //if we rebuild on 404, disable this for performance
        if ($controller === false && $this->dic->registry->get('AUTO_REBUILD_REQUEST_MAP')) {
            $this->rebuildRequestMap();
            $controller = $this->loadResource($request);
        }

        // if we still don't have a controller 404 it
        if ($controller === false) {
            $controller = $this->get404Controller();
        }

        $controller->handleRequest($request);
    }

    /**
     * @param Request $request
     * @return string
     */
    private function loadResource(Request $request) {
        $requestParts = $request->getRequestParts();
        $filename = "";
        $controller = false;
        $parts = array();
        while ($lastItem = array_pop($requestParts)) {
            $filename = implode("/", $requestParts) . "/" . $lastItem;
            if (file_exists($this->dic->tmp . $filename . ".php")) {
                $request->setRequestedResource($filename, array_reverse($parts));
                $controller = require $this->dic->tmp . $filename . ".php";
                break;
            }
            $parts[] = $lastItem;
        }

        return $controller;
    }
    
    /**
     *
     * @return Controller
     */
    public function get404Controller() { 
        if ($this->notFoundController === null) {
            $this->notFoundController = new NotFoundController();
        }
        
        return $this->notFoundController;
    }

    private function rebuildRequestMap() {
        $mapper = new RequestMapGenerator($this->dic);
        $mapper->scan($this->dic->root.'src');
    }
}