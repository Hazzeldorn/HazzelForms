<?php

namespace HazzelForms\Field\Text;

class Tel extends Text
{

    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->maxlength = $args['maxlength'] ?? 20;
        $this->regex     = $args['maxlength'] ?? '/^[+]{0,1}[- \(\)\/0-9]*$/';
        $this->fieldType = 'tel';
    }
}
