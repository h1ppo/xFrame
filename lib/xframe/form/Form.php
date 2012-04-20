<?php
namespace xframe\form;
use \xframe\form\field\Field;

/**
 * Handles the content for a form
 * @todo csrf protection
 * @todo enctype
 * @todo decorators
 */
abstract class Form {

    const METHOD_POST = "post";
    const METHOD_GET = "get";

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $fields = array();

    /**
     *
     * @param string $id
     * @param string $method
     */
    public function __construct($id, $method = Form::METHOD_POST) {
        $this->id = $id;
        $this->method = $method;
        $this->init();
    }

    abstract protected function init();

    /**
     * Adds a form field to the form
     * @param \xframe\form\field\Field $field
     * @return \xframe\form\Form
     */
    protected function addField(Field $field) {
        $this->fields[$field->getName()] = $field;
        return $this;
    }

    /**
     * Gets a field value from a specific field in the form
     * @param string $fieldName
     * @return string
     */
    public function getValue($fieldName) {
        if (array_key_exists($fieldName,  $this->fields)) {
            return $this->fields[$fieldName]->getValue();
        }
    }

    /**
     * Sets the values from the current request into the fields for this form
     * @param \xframe\request\Request $request
     */
    public function processRequest(\xframe\request\Request $request) {
        foreach ($this->fields as $name => $field) {
            $field->setValue($request->$name);
        }
    }

}

