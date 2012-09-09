<?php
namespace xframe\session;

/**
 * wrapper for the _SESSION global
 */
class Session {

    protected $namespace;

    /**
     *
     * @param string $namespace
     */
    public function __construct($namespace) {
        if (session_id() == '') {
            session_start();
        }
        $this->namespace = &$_SESSION[$namespace];
    }

    /**
     * Discards this namespace in the session
     */
    public function discard() {
        $this->namespace = null;
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function get($key) {
		if (is_array($this->namespace) && array_key_exists($key, $this->namespace)) {
			return $this->namespace[$key];
		}
		return null;
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

