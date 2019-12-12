<?php

namespace HazzelForms;

class Options extends Field {

    protected $disabled,
              $options = array(),
              $default,
              $fieldType = '';

    public function __construct($fieldName, $formName, $args = array())  {
        parent::__construct($fieldName, $formName, $args);

        $this->options      = (isset($args['options']))      ? $args['options']      : array();
        $this->default      = (isset($args['default']))      ? $args['default']      : '';
        $this->disabled     = (isset($args['disabled']))     ? $args['disabled']     : false;
    }

    protected function buildOptionAttributeString($option) {
      $attributes = '';

      if($this->disabled == true){
        $attributes .= ' disabled';
      }
      if( (empty($this->fieldValue) && $this->default == $option)
          || $this->fieldValue == $option) {
        $attributes .= ' checked';
      }

      return $attributes;
    }

    public function returnField()   {
        $fieldHtml  = '<div class="option-wrap">';

        foreach($this->options as $optionID => $option){
          $fieldHtml .= sprintf('<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s-%4$s" value="%4$s" class="%5$s" %6$s />', $this->fieldType, $this->formName, $this->fieldSlug, $optionID, $this->classlist, $this->buildOptionAttributeString($option));
          $fieldHtml .= sprintf('<label for="%1$s-%2$s-%3$s"><span class="option-caption">%4$s</span></label>', $this->formName, $this->fieldSlug, $optionID, $option);
        } unset($optionID, $option);

        $fieldHtml .= '</div>';
        return $fieldHtml;
    }

    // Build error message
    public function returnError($lang)   {
        if(!empty($this->error)){
          return sprintf('<span class="error-msg">%1$s</span>', $lang->getMessage('option_fields', $this->error));
        }
    }

    // set choice
    public function setValue($value) {

      // pre-validation
      if(Tools::containsInt($value)) {
        if($value < 0 || $value + 1 > count($this->options)) {
          // reset option because dom elements were changed by suspicious user
          $value = '';
        } elseif($value == 0 || !empty($value)){
            $this->fieldValue = (isset($this->options[$value])) ? $this->options[$value] : '';
        }
      }

    }

    public function validate() {
        $value = $this->fieldValue;

        if(empty($value) && $this->required){
            $this->error = 'empty';
        }

        $this->validated = true;
        return $this->isValid();
    }
}
