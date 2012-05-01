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
     * @var string
     */
    protected $label;

    /**
     * @var boolean
     */
    protected $required;

    /**
     * @var array
     */
    protected $validators = array();

    /**
     * \xframe\form\decorator\field\Field
     * @var type
     */
    protected $decorator;

    /**
     * @var array
     */
    protected $error = array();

    /**
     *
     * @param string $name
     * @param boolean $required
     * @param array $validators
     * @param string $label
     * @param \xframe\form\decorator\field\Field $decorator
     */
    public function __construct($name, $required = false, $validators = array(), $label = "", \xframe\form\decorator\field\Field $decorator = null) {
        $this->name = $name;
        $this->required = $required;
        $this->validators = $validators;
        $this->label = $label;
        $this->decorator = $decorator;
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
     * Returns the current label of the field
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Sets the label of this field
     * @param string $value
     * @return \xframe\form\field\Field
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    /**
     * Returns the validators for this field
     * @return array
     */
    public function getValidators() {
        return $this->validators;
    }

    /**
     * Returns true if this field is required
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }

    public function addError($error) {
        $this->error[] = $error;
    }

    /**
     * Return true if this field contains errors
     * @return boolean
     */
    public function hasErrors() {
        return count($this->error) > 0;
    }

    public function getErrors() {
        return $this->error;
    }

    public function __toString() {
        if ($this->decorator) {
            return $this->decorator->getHtml($this);
        }
        return $this->value;
    }
    
}

