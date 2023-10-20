<?php

namespace HazzelForms\Field;

use HazzelForms\Tools as Tools;

class Field
{
    protected $formName;
    protected $fieldName;
    protected $fieldSlug;
    protected $fieldType;
    protected $label = '';
    protected $required;
    protected $classlist = '';
    protected $error = '';
    protected $fieldValue = '';
    protected $validated = false;

    public function __construct($fieldName, $formName, $args = [])
    {
        $this->formName   = $formName;
        $this->fieldName  = $fieldName;
        $this->fieldSlug  = Tools::slugify($fieldName);

        $this->label      = $args['label'] ?? '';
        $this->required   = $args['required'] ?? true;
        $this->classlist  = $args['classlist'] ?? '';
    }

    // build label
    public function returnLabel()
    {
        if ($this->label != false) {
            return sprintf('<label for="%1$s-%2$s"><span class="label">%3$s</span></label>', $this->formName, $this->fieldSlug, $this->label);
        }
    }

    // Build error message
    public function returnError($lang)
    {
        if (!empty($this->error)) {
            return sprintf('<span class="error-msg">%1$s</span>', $lang->getMessage($this->fieldType, $this->error));
        }
    }

    // clear field value
    public function clear()
    {
        $this->fieldValue = '';
    }

    protected function isValid()
    {
        if ($this->validated && empty($this->error)) {
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
    public function getName()
    {
        return $this->fieldName;
    }

    public function getSlug()
    {
        return $this->fieldSlug;
    }

    public function getType()
    {
        return $this->fieldType;
    }

    public function getValue()
    {
        return html_entity_decode($this->fieldValue, ENT_COMPAT, 'UTF-8');
    }

    public function getRequired()
    {
        return $this->required;
    }

    public function getFieldWrapBefore()
    {
        $classes = $this->fieldType;
        if ($this->fieldType == 'honeypot') {
            // incognito class for honeypot
            $classes = '';
        }
        if ($this->validated && empty($this->error)) {
            $classes .= ' is-valid';
        } elseif ($this->validated && !empty($this->error)) {
            $classes .= ' has-error';
        }
        return sprintf('<div class="field-wrap %1$s %2$s">', $this->classlist, $classes);
    }

    public function getFieldWrapAfter()
    {
        return "</div>";
    }
}
