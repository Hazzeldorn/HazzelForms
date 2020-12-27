<?php

namespace HazzelForms\Field\Options;

use HazzelForms\Field\Field as Field;

class Radio extends Options
{

    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'radio';
    }

    protected function buildOptionAttributeString($option)
    {
        $attributes = parent::buildOptionAttributeString($option);

        if ($this->required) {
            $attributes .= ' required';
        }

        return $attributes;
    }
}
