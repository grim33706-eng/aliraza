<?php

/*
 * Router for PHP's built-in web server, which Wasmer Edge runs the site on.
 * Real files in public/ are served as-is; every other request (including
 * URLs with a dot, like /sitemap.xml or /storage/uploads/x.webp) goes to Laravel.
 */

$public = __DIR__.'/public';
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

if ($uri !== '/' && is_file($public.$uri)) {
    return false;
}

require $public.'/index.php';
