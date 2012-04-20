<?php
namespace xframe\form;
use form\Form;

/**
 * Handles the validation of xFrame\form\Form objects
 */
class Validator {

    /**
     *
     * @var \xframe\form\Form
     */
    private $form;

    /**
     *
     * @param \xframe\form\Form $form
     */
    public function __construct(Form $form) {
        $this->form = $form;
    }

    public function validate() {

    }

}

