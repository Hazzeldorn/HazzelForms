<?php

namespace HazzelForms\Field\Options;

use HazzelForms\Tools as Tools;
use HazzelForms\Field\Field as Field;

class Checkbox extends Options
{
    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'checkbox';
    }

    public function returnField()
    {
        $fieldHtml  = '';

        foreach ($this->options as $optionKey => $option) {
            $fieldHtml .= '<div class="option-wrap">';
            $fieldHtml .= sprintf('<input type="%1$s" name="%2$s[%3$s][]" id="%2$s-%3$s-%4$s" value="%4$s" class="%5$s" %6$s />', $this->fieldType, $this->formName, $this->fieldSlug, $optionKey, $this->classlist, $this->buildOptionAttributeString($option));
            $fieldHtml .= sprintf('<label for="%1$s-%2$s-%3$s"><span class="option-caption">%4$s</span></label>', $this->formName, $this->fieldSlug, $optionKey, $option);
            $fieldHtml .= '</div>';
        } unset($optionKey, $option);

        return $fieldHtml;
    }

    // set choice
    public function setValue($values)
    {
        foreach ($values as $chosenOption) {
            // pre validation (if key does not exist, the DOM was changed by suspicious user or nothing was selected)
            if (array_key_exists($chosenOption, $this->options)) {
                $this->fieldValue .= empty($this->fieldValue) ? '' : ', ';
                if (Tools::containsInt($chosenOption)) {
                    // keys are auto-generated -> use value
                    $this->fieldValue .= $this->options[$chosenOption];
                } else {
                    // use custom keys as value
                    $this->fieldValue .= $chosenOption;
                }
            }
        } unset($chosenOption);
    }
}
