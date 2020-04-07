<?php

namespace HazzelForms;

class Tel extends Text {

  public function __construct($fieldName, $formName, $args = array())  {
      parent::__construct($fieldName, $formName, $args);

        $this->maxlength = $args['maxlength'] ?? 20;
        $this->regex     = $args['maxlength'] ?? '/^[+]{0,1}[- \(\)\/0-9]*$/';
        $this->fieldType = 'tel';
    }

}
