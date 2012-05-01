<?php
namespace xframe\form\decorator\field;

/**
 * Field field for a hidden element
 */
class Hidden extends Field {

    public function getHtml(\xframe\form\field\Field $field) {
        return '<input type="hidden" name="'.$field->getName().'" value="'.$field->getValue().'" />';
    }

}
