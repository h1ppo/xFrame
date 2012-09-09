<?php
namespace xframe\request;

use \xframe\session\Session;

/**
 * Container for message persistence between requests
 */
class FlashMessenger {

    const TYPE_SUCCESS = 'success', TYPE_NOTICE = 'notice', TYPE_WARNING = 'warning', TYPE_ERROR = 'error';

    /**
     * Sets a value for the flash messager to persist
     * @param mixed $value
     */
    public function set($type, $value) {
        $storage = new Session(__CLASS__);
        $storage->set($type, $value);
    }

    /**
     *
     * @param string $type
     * @return mixed
     */
    public function get($type) {
        $storage = new Session(__CLASS__);
        $value = $storage->get($type);
        $storage->set($type, null);
        return $value;
    }

    /**
     *
     * @return array
     */
    public function getAll() {
        $storage = new Session(__CLASS__);
        $result = array(
            self::TYPE_ERROR => $storage->get(self::TYPE_ERROR),
            self::TYPE_WARNING => $storage->get(self::TYPE_WARNING),
            self::TYPE_NOTICE => $storage->get(self::TYPE_NOTICE),
            self::TYPE_SUCCESS => $storage->get(self::TYPE_SUCCESS)
        );

        $storage->discard();

        return $result;
    }

}
