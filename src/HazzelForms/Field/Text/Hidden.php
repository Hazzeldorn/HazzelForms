<?php

namespace HazzelForms\Field\Text;

class Hidden extends Text
{
    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType    = 'hidden';
        $this->label        = false;
        $this->autofocus    = false;
        $this->placeholder  = false;
    }

    public function getFieldWrapBefore()
    {
        return "";
    }
    public function getFieldWrapAfter()
    {
        return "";
    }

    protected function buildAttributeString()
    {
        return ' spellcheck="false"';
    }

    // no error for hidden fields
    public function returnError($lang)
    {
        return;
    }
}
