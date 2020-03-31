<?php

namespace HazzelForms;

class HoneyPot extends Captcha {

  protected $fieldType = 'honeypot';

  public function __construct($formName, $fieldName, $args = array())  {
    parent::__construct($formName, $fieldName, $args);
    $this->fieldSlug = 'hf_hp';
    $this->required  = false;
    $this->label     = false;
  }

  public function returnField()   {
    return sprintf(
      '<input type="text" name="%1$s[%2$s]" id="%1$s-%2$s" value="" tabindex="-1" autocomplete="false" class="hf_hp" />
      <style>
      .hf_hp {
        display: none !important;
      }
      </style>',
      $this->formName, $this->fieldSlug);
    }

    public function validate() {
      if(!empty($this->fieldValue)) {
        // honeypot field must be empty to be valid
        $this->error = 'invalid';
      }

      $this->validated = true;
      return $this->isValid();
    }

    public function setValue($value) {
      $this->fieldValue = $value;
    }
  }
