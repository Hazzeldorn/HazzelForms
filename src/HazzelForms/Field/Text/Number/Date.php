<?php

namespace HazzelForms\Field\Text\Number;

class Date extends Number
{
    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'date';
    }

    public function validate()
    {
        $value = $this->fieldValue;

        // Check if input is a valid date. If yes, send to parent validation function
        if (\DateTime::createFromFormat('Y-m-d', $value)) {
            parent::validate();
        } else {
            $this->error = 'invalid';
        }

        $this->validated = true;
        return $this->isValid();
    }
}
