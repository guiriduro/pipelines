<?php

/* this file is part of pipelines */

$inc = function ($file) {
    return is_file($path = __DIR__ . '/../' . $file) ? include $path : false;
};

if (!$inc('/vendor/autoload.php') and !$inc('/../../autoload.php')) {
    fputs(
        defined('STDERR') ? constant('STDERR') : fopen('php://stderr', 'w')
        , <<<ERROR
To use pipelines set up project dependencies via `composer install` first
See https://getcomposer.org/download/ for how to install Composer

ERROR
);
    exit(1);
}

unset($inc);