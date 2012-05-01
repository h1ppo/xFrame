<?php
namespace xframe\form;
use \xframe\form\field\Field;
use \xframe\form\field\Hidden;
use \xframe\session\Session;

/**
 * Handles the content for a form
 * @todo enctype
 * @todo decorators
 */
abstract class Form {

    const METHOD_POST = "post";
    const METHOD_GET = "get";
    const CSRF_NAME = "xframe_csrf_token";

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array of \xframe\form\field\Field
     */
    protected $field = array();

    /**
     * @var array
     */
    protected $error = array();

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

    public function addCSRFToken() {
        $field = new Hidden(self::CSRF_NAME, true, array(new \xframe\validation\CSRF()), null, new decorator\field\Hidden());
        $session = new Session("form");
        $token = $session->get(self::CSRF_NAME) == "" ? $this->generateCSRFToken() : $session->get(self::CSRF_NAME);
        $session->set(self::CSRF_NAME, $token);
        $field->setValue($token);
        $this->addField($field);
    }

    protected function generateCSRFToken() {
        return md5(mt_rand(1,1000000) . time() . $this->id);
    }

    /**
     * Adds a form field to the form
     * @param \xframe\form\field\Field $field
     * @return \xframe\form\Form
     */
    protected function addField(Field $field) {
        $this->field[$field->getName()] = $field;
        return $this;
    }

    /**
     * Return a field
     * @return \xframe\form\field\Field
     */
    public function getField($fieldName) {
        return $this->field[$fieldName];
    }

    /**
     * Return this forms fields
     * @return array of \xframe\form\field\Field
     */
    public function getFields() {
        return $this->field;
    }

    /**
     * Gets a field value from a specific field in the form
     * @param string $fieldName
     * @return string
     */
    public function getValue($fieldName) {
        if (array_key_exists($fieldName,  $this->field)) {
            return $this->field[$fieldName]->getValue();
        }
    }

    /**
     * Sets the values from the current request into the fields for this form
     * @param \xframe\request\Request $request
     */
    public function processRequest(\xframe\request\Request $request) {
        foreach ($this->field as $name => $field) {
            $field->setValue($request->$name);
        }
    }

    public function addError($fieldName, $error) {
        $this->error[$fieldName] = $error;
        return $this;
    }

    /**
     * Returns true if this form contains errors
     * @return boolean
     */
    public function hasErrors() {
        return count($this->error) > 0;
    }

}
