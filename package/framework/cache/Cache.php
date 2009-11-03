<?php

/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package cache
 *
 * This is a singleton for a Memcache database
 */
class Cache {
    private static $instance = false;


    /**
     * Create a Memcahe instance based on the settings in the registry
     */
    private static function connect() {
        self::$instance = new Memcache();
	}

    /**
     * Return the current Memcache instance or create one if one does not exist
     */
    public static function mch() {
        if (!self::$instance instanceof Memcache) {
            self::connect();
        }

        return self::$instance;
    }

    /**
     * Override the current instance with the given instance
     *
     * @param $newInstance Memcache new Memcache instance
     */
    public static function setInstance(Memcache $newInstance) {
        self::$instance = $newInstance;
    }


}
