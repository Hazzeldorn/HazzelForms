<?php

namespace HazzelForms\Field\Text;

class Hidden extends Text
{
    public function __construct($fieldName, $formName, $args = [])
    {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType    = 'hidden';
        $this->label        = false;
        $this->autocomplete = false;
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

    // no error for hidden fields
    public function returnError($lang)
    {
        return;
    }
}
