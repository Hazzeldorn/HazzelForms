<?php

namespace HazzelForms;

class Dropdown extends Options {

    protected $first;

    public function __construct($fieldName, $formName, $args = array())  {
        parent::__construct($fieldName, $formName, $args);

        $this->first = $args['first'] ?? '';

        $this->fieldType = 'dropdown';
    }

    protected function buildAttributeString() {
      $attributes = '';

      if($this->disabled == true){
        $attributes .= ' disabled';
      }
      if($this->required){
        $attributes .= ' required';
      }

      return $attributes;
    }

    protected function buildOptionAttributeString($option) {
      $attributes = '';

      if( (empty($this->fieldValue) && $this->default == $option)
          || $this->fieldValue == $option) {
        $attributes .= ' selected';
      }

      return $attributes;
    }


    public function returnField()   {
        $fieldHtml = sprintf('<select name="%1$s[%2$s]" class="%3$s" %4$s>', $this->formName, $this->fieldSlug, $this->classlist, $this->buildAttributeString());

        if(!empty($this->first)){
          $fieldHtml .= '<option value="">'.$this->first.'</option>';
        }
        foreach($this->options as $optionKey => $option){
          $fieldHtml .= sprintf('<option value="%1$s" %3$s>%2$s</option>', $optionKey, $option, $this->buildOptionAttributeString($option));
        } unset($optionKey, $option);

        $fieldHtml .= '</select>';


        return $fieldHtml;
    }
}
