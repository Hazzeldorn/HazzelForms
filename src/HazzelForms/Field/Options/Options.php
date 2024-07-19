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

        foreach ($this->options as $optionKey => $optionVal) {
            $fieldHtml .= '<div class="option-wrap">';
            $fieldHtml .= sprintf(
                '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s-%4$s" value="%4$s" class="%5$s" %6$s />',
                $this->fieldType,
                $this->formName,
                $this->fieldSlug,
                $optionKey,
                $this->classlist,
                $this->buildOptionAttributeString($optionKey, $optionVal)
            );
            $fieldHtml .= sprintf(
                '<label for="%1$s-%2$s-%3$s"><span class="option-caption">%4$s</span></label>',
                $this->formName,
                $this->fieldSlug,
                $optionKey,
                $optionVal
            );
            $fieldHtml .= '</div>';
        }
        unset($optionKey, $optionVal);

        return $fieldHtml;
    }

    // Build error message
    public function getErrorMessage($lang) 
    {
        if (!empty($this->error)) {
            return (count($this->options) == 1) ? $lang->getMessage('option_fields_single', $this->error) : $lang->getMessage('option_fields', $this->error);
        }
    }

    // set choice
    public function setValue($value, $origin = 'MANUAL')
    {
        if (Tools::isArrayAssociative($this->options)) {
            // associative array -> use keys
            if (array_key_exists($value, $this->options)) {
                $this->fieldValue = $value;
            }
        } else {
            // non-associative array -> use values
            if ($origin == 'MANUAL' && in_array($value, $this->options)) {
                $this->fieldValue = $value;
            } elseif (array_key_exists($value, $this->options)) {
                $this->fieldValue = $this->options[$value];
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
