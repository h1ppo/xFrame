<?php
namespace xframe\form\field;

class Select extends Field {

    /**
     * @var array
     */
    protected $options;

    /**
     *
     * @param string $name
     * @param boolean $required
     * @param array $validators
     * @param string $label
     * @param \xframe\form\decorator\field\Field $decorator
     * @param array $options
     */
    public function __construct($name,
                                $required = false,
                                $validators = array(),
                                $label = "",
                                \xframe\form\decorator\field\Field $decorator = null,
                                $options = array()) {
        parent::__construct(
            $name,
            $required,
            $validators,
            $label,
            $decorator
        );
        $this->options = $options;
    }

    /**
     * 
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

}

