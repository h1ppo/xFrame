<?php

namespace xframe\validation;

/**
 * Validate an input as a numeric digit 
 */
class Digit extends Validator {

    const DIGIT_ERR = 1, MIN_ERR = 2, MAX_ERR = 3;

    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;

    /**
     * @param int $min
     * @param int $max
     */
    public function __construct($min = null, $max = null) {
        $this->min = $min;
        $this->max = $max;
        $this->errorMessage[self::DIGIT_ERR] = "'%value%' is not a digit.";
        $this->errorMessage[self::MIN_ERR] = "'%value%' is less than minimum, '%min%'.";
        $this->errorMessage[self::MAX_ERR] = "'%value%' is greater than maximum, '%max%'.";
    }

    /**
     * Checks if a given value contains only digits and is within the min and
     * max constraints
     * 
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) {
        if (!ctype_digit("{$value}")) {
            $this->error(self::DIGIT_ERR, array("%value%" => $value));
        }
        if ($this->min != null && $value < $this->min) {
            $this->error(self::MIN_ERR, array("%value%" => $value, '%min%' => $this->min));
        }
        if ($this->max != null && $value > $this->max) {
            $this->error(self::MAX_ERR, array("%value%" => $value, '%max%' => $this->max));
        }
        
        return true;
    }

}

