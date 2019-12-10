<?php

namespace HazzelForms;

class Hidden extends Text {

    public function __construct($fieldName, $formName, $args = array())  {
      parent::__construct($fieldName, $formName, $args);

      $this->fieldType    = 'hidden';
      $this->label        = false;
      $this->autocomplete = false;
      $this->placeholder  = false;
    }

    public function getFieldWrapBefore() {
      return "";
    }
    public function getFieldWrapAfter() {
      return "";
    }

}
