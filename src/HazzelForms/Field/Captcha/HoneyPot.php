<?php

namespace HazzelForms\Field\Captcha;

use HazzelForms\Field\Field as Field;

class HoneyPot extends Captcha {
    protected $inlineCSS;
    protected $fieldType = 'honeypot';

    public function __construct($formName, $fieldName, $args = []) {
        parent::__construct($formName, $fieldName, $args);
        $this->inlineCSS = $args['inline-css'] ?? true;

        $this->fieldSlug = 'hf_hp';
        $this->required  = false;
        $this->label     = false;
    }

    public function returnField() {
        $fieldHtml = '';

        if ($this->inlineCSS) {
            $this->classlist .= ' ' . $this->fieldSlug;
            $fieldHtml .= "<style> .{$this->fieldSlug} { display: none !important; } </style>";
        }

        return $fieldHtml .= sprintf(
            '<input type="text" name="%1$s[%2$s]" id="%1$s-%2$s" value="%3$s" tabindex="-1" autocomplete="false" class="%4$s" />',
            $this->formName,
            $this->fieldSlug,
            $this->fieldValue,
            $this->classlist
        );
    }


    public function setValue($value, $origin = 'MANUAL') {
        $this->fieldValue = htmlspecialchars(trim($value));
    }

    public function validate() {
        if (!empty($this->fieldValue)) {
            // honeypot field must be empty to be valid
            $this->error = 'invalid';
        }

        $this->validated = true;
        return $this->isValid();
    }
}
