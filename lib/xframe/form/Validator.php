<?php
namespace xframe\form;
use \xframe\form\Form;
use \xframe\validation\Exception;

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

                try {
                    $validator->validate($field->getValue());
                } catch (Exception $ex) {
                    $field->addError($ex->getMessage());
                    $this->form->addError($fieldName, 1);
                    $this->valid = false;
                }
                
            }
        }
        return $this->valid;
    }

}

