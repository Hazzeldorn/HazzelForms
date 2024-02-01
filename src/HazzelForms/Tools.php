<?php

namespace HazzelForms;

class Tools {
    /**
     * Strip out all empty characters from a string
     *
     * @param  string $val
     * @return string
     */
    public static function stripper($val) {
        foreach ([' ', '&nbsp;', '\n', '\t', '\r'] as $strip) {
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

    /**
     * Helper function to check if a variable (numeric or string) actually holds a valid integer value
     *
     * @param  string $var (mixed)
     * @return boolean
     */
    public static function containsInt($var) {
        return filter_var($var, FILTER_VALIDATE_INT) !== false;
    }


    /**
     * Check if an array has numeric indexes or key-value pairs (associative)
     */
    public static function isArrayAssociative($array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
