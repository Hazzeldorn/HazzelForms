<?php

namespace HazzelForms;

class Date extends Number {

  public function __construct($fieldName, $formName, $args = array())  {
      parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'date';
    }

    public function returnField()   {
        return sprintf('<input type="%1$s" name="%2$s[%3$s]" value="%4$s" class="%5$s" %6$s />', $this->fieldType, $this->formName, $this->fieldSlug, $this->fieldValue, $this->classlist, $this->buildAttributeString());
    }

    public function validate() {
        $value = $this->fieldValue;

        // Check if input is a valid date. If yes, send to parent validation function
        if (\DateTime::createFromFormat('Y-m-d', $value)){
          parent::validate();
        }
        else {
          $this->error = 'invalid';
        }

        $this->validated = true;
        return $this->isValid();
    }

}
