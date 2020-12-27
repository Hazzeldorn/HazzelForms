<?php

namespace HazzelForms;

class Language
{

    protected $language;
    protected $strings;
    protected $dir = __DIR__ . '/translation'; // directory to language files

    public function __construct($language = 'EN')
    {
        $this->language = $language;

        // load language file
        $this->strings = json_decode(file_get_contents($this->dir . "/" . strtoupper($this->language) . ".json"), true);
    }

    // GETTERS & SETTERS
    public function getMessage($fieldType, $noticeType)
    {
        return $this->strings[$fieldType][$noticeType];
    }
}
