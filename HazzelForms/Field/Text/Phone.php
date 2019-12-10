<?php

namespace HazzelForms;

class Phone extends Text {

  public function __construct($fieldName, $formName, $args = array())  {
      parent::__construct($fieldName, $formName, $args);

        $this->maxlength = (isset($args['maxlength']))   ? $args['maxlength']    : 20;
    }

    public function validate() {
        if (parent::validate()) {
            if ( !empty($this->fieldValue) && !preg_match('/^[+]{0,1}[- \/0-9]*$/', $this->fieldValue)  ) {
                $this->error = 'invalid';
            }
        }
        $this->validated = true;
        return $this->isValid();
    }

}
