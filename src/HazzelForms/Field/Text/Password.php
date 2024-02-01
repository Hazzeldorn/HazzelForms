<?php

namespace HazzelForms\Field\Text;

class Password extends Text {
    public function __construct($fieldName, $formName, $args = []) {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'password';
    }

    public function returnField() {
        // returning field with cleared fieldValue on case of error (for safety and XSS prevention)
        return sprintf('<input type="%1$s" id="%2$s-%3$s" name="%2$s[%3$s]" value="" class="%4$s" %5$s />', $this->fieldType, $this->formName, $this->fieldSlug, $this->classlist, $this->buildAttributeString());
    }


    public function setValue($value, $origin = 'MANUAL') {
        $this->fieldValue = $value; // override  encoding
    }

    public function getValue() {
        return $this->fieldValue; // override  encoding
    }
}
