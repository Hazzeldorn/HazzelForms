<?php

namespace HazzelForms;

class Range extends Number {

  public function __construct($fieldName, $formName, $args = array())  {
      parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'range';
    }

}
