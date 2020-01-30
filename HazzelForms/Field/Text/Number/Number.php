<?php

namespace HazzelForms;

class Number extends Text {

  protected $min,
            $max,
            $step;

    public function __construct($fieldName, $formName, $args = array())  {
      parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'number';
        $this->min                     = (isset($args['min']))         ? $args['min']          : '';
        $this->max                     = (isset($args['max']))         ? $args['max']          : '';
        $this->step                    = (isset($args['step']))        ? $args['step']         : '';
    }

    protected function buildAttributeString() {
      $attributes = parent::buildAttributeString();

      if(!empty($this->step)){
        $attributes .= ' step="'.$this->step.'"';
      }
      if(!empty($this->min) || $this->min === 0){
        $attributes .= ' min="'.$this->min.'"';
      }
      if(!empty($this->max || $this->max === 0)) {
        $attributes .= ' max="'.$this->max.'"';
      }

      return $attributes;
    }

    public function returnField()   {
        return sprintf('<input type="%1$s" id="%2$s-%3$s" name="%2$s[%3$s]" value="%4$s" class="%5$s" %6$s />', $this->fieldType, $this->formName, $this->fieldSlug, $this->fieldValue, $this->classlist, $this->buildAttributeString());
    }

    public function validate() {
        $value = $this->fieldValue;

        if (parent::validate()) {
            if (!empty($this->fieldValue) && !filter_var($this->fieldValue, FILTER_VALIDATE_FLOAT) && get_class($this) == 'HazzelForms\Number') {
                $this->error = 'invalid';
            }
            if (!empty($this->min) && !empty($this->fieldValue)) {
                // check min (never trust browsers...)
                if ($value < $this->min) { // works for dates too when format is Y-m-d :)
                    $this->error = 'too_small';
                }
            }
            if (!empty($this->max) && !empty($this->fieldValue)) {
                // check max (never trust browsers...)
                if ($value > $this->max) {
                    $this->error = 'too_big';
                }
            }
        }
        $this->validated = true;
        return $this->isValid();
    }

}
