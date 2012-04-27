<?php

namespace xframe\validation;
use \xframe\session\Session;

/**
 * Validate an input against a csrf token in the session
 */
class CSRF extends Validator {

    const MATCH_ERR = 1;

    public function __construct() {
        $this->errorMessage[self::MATCH_ERR] = "'%value%' doesn't match session.";
    }

    /**
     * Checks a match against the csfr token
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) {
        $session = new Session("form");
        $token = $session->get(\xframe\form\Form::CSRF_NAME);
        if ($value != $token) {
            $this->error(self::MATCH_ERR, array("%value%" => $value));
        }

        return true;
    }

}

