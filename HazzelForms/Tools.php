<?php

namespace HazzelForms;

class Tools {

    /**
     * Strip out all empty characters from a string
     *
     * @param string $val
     * @return string
     */
    public static function stripper($val) {
        foreach (array(' ', '&nbsp;', '\n', '\t', '\r') as $strip) {
            $val = str_replace($strip, '', (string) $val);
        }
        return $val === '' ? false : $val;
    }

    /**
     * Slugify a string using a specified replacement for empty characters
     *
     * @param string $text
     * @param string $replacement
     *
     * @return string
     */
    public static function slugify($text, $replacement = '_') {
        return strtolower(trim(preg_replace('/\s/', $replacement, $text), '_'));
    }

}
