<?php
namespace xframe\validation;

abstract class Validator {

    /**
     * An array of error messages
     * @var array
     */
    protected $errorMessage = array();

    /**
     * Takes a reference to an error string and replaces any text which
     * needs to be replaced with actual values and throws an exception with
     * this message
     * @param string $index
     * @param array $replacement format of '%replace this%' => 'with this'
     * @return string
     */
    protected function error($index, $replacement = array()) {
        if (!array_key_exists($index, $this->errorMessage)) {
            return $index;
        }

        $errorString = str_replace(array_keys($replacement), $replacement, $this->errorMessage[$index]);

        throw new Exception($errorString);
    }

    /**
     * Perform the validation of the given value
     * 
     * @param string $value
     * @throws xframe\validation\Exception
     * @return boolean
     */
    abstract public function validate($value);

}
