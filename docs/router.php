<?php

$__REQUEST_URI = $_SERVER['REQUEST_URI'];
$__DIR = str_replace("\\", "/", __DIR__);
$__DOCUMENT_ROOT = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);

$__REQUEST_LOCATION = $__DIR . $__REQUEST_URI;


if ($__DIR !== $__DOCUMENT_ROOT) {
    $__REQUEST_LOCATION = $__DIR . str_replace($__DIR, '', $__DOCUMENT_ROOT . $__REQUEST_URI);
}

spl_autoload_register(function ($request) use ($__REQUEST_LOCATION, $__REQUEST_URI, $__DOCUMENT_ROOT, $__DIR) {
    $request = $__DIR . '/Classes/' . ltrim(str_replace("\\", "/", $request), '/') . '.php';
    if (file_exists($request)) {
        @require_once $request . '';
    }
    else{
        echo $request;
    }
});

if (file_exists($__REQUEST_LOCATION) && is_file($__REQUEST_LOCATION)) {
    return false; //default built-in server routing , for other server must be implement by config file like .htaccess
} else if (file_exists($__REQUEST_LOCATION . '.php') && is_file($__REQUEST_LOCATION . '.php')) {
    ob_start();
    include $__REQUEST_LOCATION . '.php';
    ob_end_flush();
    return true;
} else if (file_exists($__REQUEST_LOCATION . '.html') && is_file($__REQUEST_LOCATION . '.html')) {
    ob_start();
    include $__REQUEST_LOCATION . '.html';
    ob_end_flush();
    return true;
} else if (file_exists($__REQUEST_LOCATION . '/index.php') && is_file($__REQUEST_LOCATION . '/index.php')) {
    ob_start();
    include $__REQUEST_LOCATION . '/index.php';
    ob_end_flush();
    return true;
} else {
    echo "File Not Found | Error 404";

    return true;
}