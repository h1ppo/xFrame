<?php
namespace xframe\session;

/**
 * wrapper for the _SESSION global
 */
class Session {

    protected $namespace;

    /**
     *
     * @param string $namepsace
     */
    public function __construct($namepsace) {
        if (session_id() == '') {
            session_start();
        }
        $this->namespace = &$_SESSION[$namepsace];
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return $this->namespace[$key];
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->namespace[$key] = $value;
    }

}

