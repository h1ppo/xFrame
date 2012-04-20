<?php
namespace xframe\form;
use \xframe\form\Form;

/**
 * Handles the validation of xFrame\form\Form objects
 */
class Validator {

    /**
     * @var \xframe\form\Form
     */
    private $form;

    /**
     * @var boolean 
     */
    private $valid;

    /**
     *
     * @param \xframe\form\Form $form
     */
    public function __construct(Form $form) {
        $this->form = $form;
        $this->valid = true;
    }

    /**
     * Validate all of the fields in the form
     * @return boolean
     */
    public function validate() {
        foreach ($this->form->getFields() as $fieldName => $field) {
            foreach ($field->getValidators() as $validator) {
                if (!$validator->validate($field->getValue())) {
                    $field->addError(1);
                    $this->form->addError($fieldName, 1);
                    $this->valid = false;
                }
            }
        }
        return $this->valid;
    }

}

