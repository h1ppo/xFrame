<?php
namespace xframe\form\field;

/**
 * Abstract class to manage individual form fields.
 */
abstract class Field {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var boolean
     */
    protected $required;

    /**
     * @var array
     */
    protected $validators = array();

    /**
     *
     * @param string $name
     * @param boolean $required
     * @param string $validators
     */
    public function __construct($name, $required = false, $validators = array()) {
        $this->name = $name;
        $this->required = $required;
        $this->validators = $validators;
    }

    /**
     * Returns the name fo the field
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the current value of the field
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets the value of this field
     * @param string $value
     * @return \xframe\form\field\Field
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Returns true if this field is required
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }
    
}

