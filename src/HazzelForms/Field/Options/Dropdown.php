<?php

namespace HazzelForms\Field\Options;

use HazzelForms\Tools as Tools;
use HazzelForms\Field\Field as Field;

class Dropdown extends Options {
    protected $first;

    public function __construct($fieldName, $formName, $args = []) {
        parent::__construct($fieldName, $formName, $args);

        $this->first = $args['first'] ?? '';

        $this->fieldType = 'dropdown';
    }

    protected function buildAttributeString() {
        $attributes = '';

        if ($this->disabled == true) {
            $attributes .= ' disabled';
        }
        if ($this->required) {
            $attributes .= ' required';
        }

        return $attributes;
    }

    protected function buildOptionAttributeString($optionKey, $optionVal) {
        $attributes = '';

        if (
            (Tools::isArrayAssociative($this->options) && ( // if options are associative, use keys
                (empty($this->fieldValue) && $this->default === $optionKey)
                || $this->fieldValue === $optionKey
            )) || ( // if options are not associative, use values
                (empty($this->fieldValue) && $this->default === $optionVal)
                || $this->fieldValue === $optionVal
            )
        ) {
            $attributes .= ' selected';
        }

        return $attributes;
    }


    public function returnField() {
        $fieldHtml = sprintf('<select name="%1$s[%2$s]" class="%3$s" %4$s>', $this->formName, $this->fieldSlug, $this->classlist, $this->buildAttributeString());

        if (!empty($this->first)) {
            $fieldHtml .= '<option value="">' . $this->first . '</option>';
        }
        foreach ($this->options as $optionKey => $optionVal) {
            $fieldHtml .= sprintf('<option value="%1$s" %3$s>%2$s</option>', $optionKey, $optionVal, $this->buildOptionAttributeString($optionKey, $optionVal));
        }
        unset($optionKey, $optionVal);

        $fieldHtml .= '</select>';


        return $fieldHtml;
    }
}
