<?php
namespace HazzelForms;

use HazzelForms\Tools;

class Field {

      protected $formName,
                $fieldName,
                $fieldSlug,
                $fieldType,
                $label = '',
                $required,
                $classList = '',
                $error = '',
                $fieldValue = '',
                $validated = false;

      public function __construct($fieldName, $formName, $args = array())  {
          $this->formName   = $formName;
          $this->fieldName  = $fieldName;
          $this->fieldSlug  = Tools::slugify($fieldName);
          $this->label      = (isset($args['label']))     ? $args['label']      : '';
          $this->required   = (isset($args['required']))  ? $args['required']   : true;
          $this->classlist  = (isset($args['classlist'])) ? $args['classlist']  : '';
      }

      // build label
      public function returnLabel()   {
        if($this->label != false){
          return sprintf('<span class="label">%1$s</span>', $this->label);
        }
      }

       // Build error message
       public function returnError($lang)   {
           if(!empty($this->error)){
             return sprintf('<span class="error-msg">%1$s</span>', $lang->getMessage($this->fieldType, $this->error));
           }
       }

       protected function isValid() {
         if($this->validated && empty($this->error)){
           $this->classlist .= ' is-valid';
           return true;
         } else {
           $this->classlist .= ' has-error';
           return false;
         }
       }

     /**
     * Getters and Setters
     */
     public function getName() {
         return $this->fieldName;
     }

     public function getSlug() {
         return $this->fieldSlug;
     }

     public function getValue() {
         return $this->fieldValue;
     }

     public function getRequired() {
         return $this->required;
     }

     public function getFieldWrapBefore() {
       $classes = $this->fieldType;
       if($this->validated && empty($this->error)){
         $classes .= ' is-valid';
       } elseif($this->validated && !empty($this->error)) {
         $classes .= ' has-error';
       }
       return sprintf('<div class="field-wrap %1$s">', $classes);
     }

     public function getFieldWrapAfter() {
       return "</div>";
     }
}
