<?php

namespace HazzelForms;

class Captcha extends Field {

  public function __construct($formName, $fieldName, $args = array())  {
      parent::__construct($formName, $fieldName, $args);
  }

  // no error on field
  public function returnError($lang)   {
      return;
  }

}

?>
