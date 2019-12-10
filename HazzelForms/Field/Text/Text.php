<?php

namespace HazzelForms;

class Text extends Field {

    protected $placeholder,
              $maxlength,
              $readonly,
              $label,
              $regex,
              $autofocus,
              $fieldType = 'text';

    public function __construct($fieldName, $formName, $args = array())  {
        parent::__construct($fieldName, $formName, $args);

        $this->placeholder  = (isset($args['placeholder']))  ? $args['placeholder']  : '';
        $this->maxlength    = (isset($args['maxlength']))    ? $args['maxlength']    : '';
        $this->readonly     = (isset($args['readonly']))     ? $args['readonly']     : false;
        $this->label        = (isset($args['label']))        ? $args['label']        : '';
        $this->regex        = (isset($args['regex']))        ? $args['regex']        : '';
        $this->autofocus    = (isset($args['autofocus']))    ? $args['autofocus']    : false;
    }


    protected function buildAttributeString() {
      $attributes = '';

      if(!empty($this->maxlength)){
        $attributes .= ' maxlength="'.$this->maxlength.'"';
      }
      if(!empty($this->placeholder)){
        $attributes .= ' placeholder="'.$this->placeholder.'"';
      }
      if($this->readonly == true){
        $attributes .= ' readonly';
      }
      if($this->autofocus){
        $attributes .= ' autofocus';
      }
      if($this->required){
        $attributes .= ' required';
      }

      return $attributes;
    }

    public function returnField()   {
        return sprintf('<input type="%1$s" name="%2$s[%3$s]" value="%4$s" class="%5$s" %6$s />', $this->fieldType, $this->formName, $this->fieldSlug, $this->fieldValue, $this->classlist, $this->buildAttributeString());
    }

    public function setValue($value) {
      $this->fieldValue = htmlspecialchars(trim($value));
    }

    public function validate() {
        $value = Tools::stripper($this->fieldValue);
        
        if (empty($value) && $this->required){
            $this->error = 'empty';
        }
        elseif(!empty($value)) {
          // if field is not empty then check value
          if (!empty($this->maxlength)) {
              // check maxlength (never trust browsers...)
              if (strlen($value) > $this->maxlength) {
                  $this->error = 'invalid';
              }
          }

          if (!empty($this->regex)) {
              // do regex test
              if (!preg_match($this->regex, $value)) {
                  $this->error = 'invalid';
              }
          }
        }

        $this->validated = true;
        return $this->isValid();
    }
}
