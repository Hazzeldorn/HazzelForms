<?php

namespace HazzelForms\Field\Options;

use HazzelForms\Tools as Tools;
use HazzelForms\Field\Field as Field;

class Radio extends Options {
    public function __construct($fieldName, $formName, $args = []) {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'radio';
    }

    protected function buildOptionAttributeString($optionKey, $optionVal) {
        $attributes = '';

        if ($this->disabled == true) {
            $attributes .= ' disabled';
        }

        if (
            (Tools::isArrayAssociative($this->options) && ( // if options are associative, use keys
                (empty($this->fieldValue) && $this->default === $optionKey)
                || $this->fieldValue === $optionKey
            )) || ( // if options are not associative, use values
                (empty($this->fieldValue) && $this->default === $optionVal)
                || $this->fieldValue === $optionVal
            )
        ) {
            $attributes .= ' checked';
        }

        if ($this->required) {
            $attributes .= ' required';
        }

        return $attributes;
    }
}
