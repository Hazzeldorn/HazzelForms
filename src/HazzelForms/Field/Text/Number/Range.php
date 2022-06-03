<?php

namespace HazzelForms\Field\Text\Number;

class Range extends Number
{
    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'range';
    }
}
