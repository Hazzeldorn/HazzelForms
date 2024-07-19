<?php

namespace HazzelForms\Field\Text;

use HazzelForms\Tools as Tools;
use HazzelForms\Field\Field as Field;

class Text extends Field
{
    protected $placeholder;
    protected $maxlength;
    protected $readonly;
    protected $regex;
    protected $autofocus;
    protected $fieldType = 'text';

    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->placeholder  = $args['placeholder'] ?? '';
        $this->maxlength    = $args['maxlength'] ?? '';
        $this->readonly     = $args['readonly'] ?? false;
        $this->regex        = $args['regex'] ?? '';
        $this->autofocus    = $args['autofocus'] ?? false;
    }


    protected function buildAttributeString()
    {
        $attributes = '';

        if (!empty($this->maxlength)) {
            $attributes .= ' maxlength="' . $this->maxlength . '"';
        }
        if (!empty($this->placeholder)) {
            $attributes .= ' placeholder="' . $this->placeholder . '"';
        }
        if ($this->readonly == true) {
            $attributes .= ' readonly';
        }
        if ($this->autofocus) {
            $attributes .= ' autofocus';
        }
        if ($this->required) {
            $attributes .= ' required';
        }

        return $attributes;
    }

    public function returnField()
    {
        return sprintf('<input type="%1$s" id="%2$s-%3$s" name="%2$s[%3$s]" value="%4$s" class="%5$s" %6$s />', $this->fieldType, $this->formName, $this->fieldSlug, $this->fieldValue, $this->classlist, $this->buildAttributeString());
    }

    public function setValue($value, $origin = 'MANUAL')
    {
        $this->fieldValue = htmlspecialchars(trim($value), ENT_COMPAT, 'UTF-8', false);
    }

    public function validate()
    {
        $value = Tools::stripper($this->fieldValue);

        if (empty($value) && $this->required) {
            $this->error = 'empty';
        } elseif (!empty($value)) {
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
