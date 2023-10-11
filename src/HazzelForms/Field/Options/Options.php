<?php

namespace HazzelForms\Field\Options;

use HazzelForms\Tools as Tools;
use HazzelForms\Field\Field as Field;

class Options extends Field
{
    protected $disabled;
    protected $options = [];
    protected $default;
    protected $fieldType = '';

    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->options = $args['options'] ?? [];
        $this->default = $args['default'] ?? '';
        $this->disabled = $args['disabled'] ?? false;
    }

    protected function buildOptionAttributeString($option)
    {
        $attributes = '';

        if ($this->disabled == true) {
            $attributes .= ' disabled';
        }
        if (
            (empty($this->fieldValue) && $this->default == $option)
            || $this->fieldValue == $option
        ) {
            $attributes .= ' checked';
        }

        return $attributes;
    }

    // build label
    public function returnLabel()
    {
        if ($this->label != false) {
            return sprintf('<label><span class="label">%1$s</span></label>', $this->label);
        }
    }

    public function returnField()
    {
        $fieldHtml  = '';

        foreach ($this->options as $optionKey => $option) {
            $fieldHtml .= '<div class="option-wrap">';
            $fieldHtml .= sprintf(
                '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s-%4$s" value="%4$s" class="%5$s" %6$s />',
                $this->fieldType,
                $this->formName,
                $this->fieldSlug,
                $optionKey,
                $this->classlist,
                $this->buildOptionAttributeString($option)
            );
            $fieldHtml .= sprintf(
                '<label for="%1$s-%2$s-%3$s"><span class="option-caption">%4$s</span></label>',
                $this->formName,
                $this->fieldSlug,
                $optionKey,
                $option
            );
            $fieldHtml .= '</div>';
        } unset($optionKey, $option);

        return $fieldHtml;
    }

    // Build error message
    public function returnError($lang)
    {
        if (!empty($this->error)) {
            return sprintf(
                '<span class="error-msg">%1$s</span>',
                (count($this->options) == 1) ? $lang->getMessage('option_fields_single', $this->error) : $lang->getMessage('option_fields', $this->error)
            );
        }
    }

    // set choice
    public function setValue($value)
    {

        // pre validation (if key does not exist, the DOM was changed by suspicious user or nothing was selected)
        if (array_key_exists($value, $this->options)) {
            if (Tools::containsInt($value)) {
                // keys are auto-generated -> use value
                $this->fieldValue = $this->options[$value];
            } else {
                // use custom keys as value
                $this->fieldValue = $value;
            }
        }
    }

    public function validate()
    {

        if (empty($this->fieldValue) && $this->required) {
            $this->error = 'empty';
        }

        $this->validated = true;
        return $this->isValid();
    }
}
