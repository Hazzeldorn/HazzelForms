<?php

namespace HazzelForms\Field\Text;

class Url extends Text
{
    private const URL_REGEX = "@(http://|https://)?[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}(\/\S*)?@"; // lazy URL validation


    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->strict = $args['strict'] ?? true;

        $this->fieldType = 'url';
    }


    public function validate()
    {
        if (parent::validate()) {
            if ($this->strict) {
                // strict validation requires protocol prefix (http:// or https://)
                if (!empty($this->fieldValue) && !filter_var($this->fieldValue, FILTER_VALIDATE_URL)) {
                    $this->error = 'invalid';
                }
            } else {
                if (!empty($this->fieldValue) && !preg_match(self::URL_REGEX, $this->fieldValue)) {
                    $this->error = 'invalid';
                }
            }
        }
        $this->validated = true;
        return $this->isValid();
    }
}
