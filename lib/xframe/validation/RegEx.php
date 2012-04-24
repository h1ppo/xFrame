<?php

namespace xframe\validation;

/**
 * Provides regular expression validation of strings 
 */
class Regex extends Validator {

    const PREG_ERR = 1, NO_MATCH_ERR = 2;
    
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var int
     */
    protected $flags;

    /**
     * @var offset
     */
    protected $offset;

    /**
     *
     * @param string $pattern
     * @param int $flags
     * @param int $offset
     */
    public function __construct($pattern, $flags = 0, $offset = 0) {
        $this->pattern = $pattern;
        $this->flags = $flags;
        $this->offset = $offset;
        $this->errorMessage[self::PREG_ERR] = "Error in matching pattern '%pattern%' against value '%value%'.";
        $this->errorMessage[self::NO_MATCH_ERR] = "No matches found for pattern '%pattern%' against value '%value%'.";
    }

    /**
     * Checkes if a given value matches a regular expression pattern
     * 
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) {
        $result = preg_match(
            $this->pattern, 
            $value, 
            $null,
            $this->flags, 
            $this->offset
        );

        if ($result === false) {
            $this->error(self::PREG_ERR, array('%pattern%' => $this->pattern, '%value%' => $value));
        }
        else if ($result == 0) {
            $this->error(self::NO_MATCH_ERR, array('%pattern%' => $this->pattern, '%value%' => $value));
        }
        
        return (boolean) $result;
    }

}
