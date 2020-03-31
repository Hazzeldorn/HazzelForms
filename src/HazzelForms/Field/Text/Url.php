<?php

namespace HazzelForms;

class Url extends Text {

    public function __construct($fieldName, $formName, $args = array())  {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'url';
    }

    public function validate() {
        if (parent::validate()) {
            if (!empty($this->fieldValue) && !filter_var($this->fieldValue, FILTER_VALIDATE_URL)) {
                $this->error = 'invalid';
            }
        }
        $this->validated = true;
        return $this->isValid();
    }

}
