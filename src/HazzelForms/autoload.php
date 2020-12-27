<?php

/**
 * PSR-4 autoloader
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 *
 * @param  string $class The fully-qualified class name.
 * @return void
 */

spl_autoload_register(function ($class) {

     $prefix = 'HazzelForms\\';
    $base_dir = __DIR__ . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

     $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        include $file;
    }
});
