<?php

namespace HazzelForms;

class Checkbox extends Options {

  public function __construct($fieldName, $formName, $args = array())  {
      parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'checkbox';
    }

    public function returnField()   {
        $fieldHtml  = '';

        foreach($this->options as $optionID => $option){
          $fieldHtml .= '<div class="option-wrap">';
          $fieldHtml .= sprintf('<input type="%1$s" name="%2$s[%3$s][]" id="%2$s-%3$s-%4$s" value="%4$s" class="%5$s" %6$s />', $this->fieldType, $this->formName, $this->fieldSlug, $optionID, $this->classlist, $this->buildOptionAttributeString($option));
          $fieldHtml .= sprintf('<label for="%1$s-%2$s-%3$s"><span class="option-caption">%4$s</span></label>', $this->formName, $this->fieldSlug, $optionID, $option);
          $fieldHtml .= '</div>';
        } unset($optionID, $option);

        return $fieldHtml;
    }

    // set choice
    public function setValue($value) {
      foreach($value as $chosenOption){

          // pre-validation
          if(Tools::containsInt($chosenOption)){
            if($chosenOption < 0 || $chosenOption + 1 > count($this->options)) {
              // reset option because it was changed by suspicious user
              $chosenOption = '';
            } elseif($chosenOption == 0 || !empty($chosenOption)){
              if(!empty($this->fieldValue)){
                $this->fieldValue .= ', ';
              }
              $this->fieldValue .= $this->options[$chosenOption];
            }
          }

      } unset($chosenOption);
    }

}
