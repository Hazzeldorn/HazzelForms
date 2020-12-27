<?php

namespace HazzelForms\Field\Text;

class Email extends Text
{

    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'email';
    }

    public function validate()
    {
        if (parent::validate()) {
            if (!empty($this->fieldValue) && !filter_var($this->fieldValue, FILTER_VALIDATE_EMAIL)) {
                $this->error = 'invalid';
            }
        }
        $this->validated = true;
        return $this->isValid();
    }
}
