<?php

namespace HazzelForms\Field\Text\Number;

class Time extends Number
{

    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'time';
    }

    public function validate()
    {
        $value = $this->fieldValue;

        // Check if input is a valid time. If yes, send to parent validation function
        if (\DateTime::createFromFormat('H:i', $value)) {
            parent::validate();
        } else {
            $this->error = 'invalid';
        }

        $this->validated = true;
        return $this->isValid();
    }
}
