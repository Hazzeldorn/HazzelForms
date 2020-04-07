<?php

namespace HazzelForms;

class Textarea extends Text {

  protected $cols,
            $rows;

  public function __construct($fieldName, $formName, $args = array())  {
      parent::__construct($fieldName, $formName, $args);

      $this->cols   = $args['cols'] ?? '';
      $this->rows   = $args['rows'] ?? '';

      $this->fieldType = 'textarea';
  }

  protected function buildAttributeString() {
    $attributes = parent::buildAttributeString();

    if(!empty($this->cols)){
      $attributes .= ' cols="'.$this->cols.'"';
    }
    if(!empty($this->rows)){
      $attributes .= ' rows="'.$this->rows.'"';
    }

    return $attributes;
  }

  public function returnField()   {
      return sprintf('<textarea id="%1$s-%2$s" name="%1$s[%2$s]" class="%4$s" %5$s>%3$s</textarea>', $this->formName, $this->fieldSlug, $this->fieldValue, $this->classlist, $this->buildAttributeString());
  }

}
