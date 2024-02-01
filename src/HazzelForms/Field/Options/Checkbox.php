<?php

namespace HazzelForms\Field\Options;

use HazzelForms\Tools as Tools;
use HazzelForms\Field\Field as Field;

class Checkbox extends Options {
    public function __construct($fieldName, $formName, $args = []) {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'checkbox';
    }

    protected function buildOptionAttributeString($optionKey, $optionVal) {
        $attributes = '';
        $selectedOptions = explode(', ', $this->fieldValue);

        if ($this->disabled == true) {
            $attributes .= ' disabled';
        }

        if (
            (Tools::isArrayAssociative($this->options) && ( // if options are associative, use keys
                (empty($this->fieldValue) && $this->default === $optionKey)
                || in_array($optionKey, $selectedOptions)
            )) || ( // if options are not associative, use values
                (empty($this->fieldValue) && $this->default === $optionVal)
                || in_array($optionVal, $selectedOptions)
            )
        ) {
            $attributes .= ' checked';
        }

        return $attributes;
    }

    public function returnField() {
        $fieldHtml  = '';

        foreach ($this->options as $optionKey => $optionVal) {
            $fieldHtml .= '<div class="option-wrap">';
            $fieldHtml .= sprintf('<input type="%1$s" name="%2$s[%3$s][]" id="%2$s-%3$s-%4$s" value="%4$s" class="%5$s" %6$s />', $this->fieldType, $this->formName, $this->fieldSlug, $optionKey, $this->classlist, $this->buildOptionAttributeString($optionKey, $optionVal));
            $fieldHtml .= sprintf('<label for="%1$s-%2$s-%3$s"><span class="option-caption">%4$s</span></label>', $this->formName, $this->fieldSlug, $optionKey, $optionVal);
            $fieldHtml .= '</div>';
        }
        unset($optionKey, $optionVal);

        return $fieldHtml;
    }

    // set choice
    public function setValue($values, $origin = 'MANUAL') {

        // if value is not an array, make it one
        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $chosenOption) {
            $this->fieldValue .= empty($this->fieldValue) ? '' : ', ';

            var_dump($this->fieldValue);

            if (Tools::isArrayAssociative($this->options)) {
                // associative array -> use keys
                if (array_key_exists($chosenOption, $this->options)) {
                    $this->fieldValue .= $chosenOption;
                }
            } else {
                // non-associative array -> use values
                if ($origin == 'MANUAL' && in_array($chosenOption, $this->options)) {
                    $this->fieldValue .= $chosenOption;
                } else if (array_key_exists($chosenOption, $this->options)) {
                    $this->fieldValue .= $this->options[$chosenOption];
                }
            }
        }
        unset($chosenOption);

        var_dump($this->fieldValue);
    }
}
