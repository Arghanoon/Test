<?php

$__REQUEST_SPLIT = explode("?", $_SERVER['REQUEST_URI'], 2);
$__REQUEST_URI = $__REQUEST_SPLIT[0];//preg_replace("/\?.*/", '', $_SERVER['REQUEST_URI']);
$__REQUEST_QUERY_STRING = $__REQUEST_SPLIT[1] ?? "";

$__DIR = str_replace("\\", "/", __DIR__);
$__DOCUMENT_ROOT = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
$__SERVER = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$__FULL_REQUEST = "{$__SERVER}$_SERVER[REQUEST_URI]";
define("__SERVER", $__SERVER);
define("__REQUEST", $__REQUEST_URI);
define("__FULL_REQUEST", $__FULL_REQUEST);
$__REQUEST_LOCATION = $__DIR . $__REQUEST_URI;

if ($__DIR !== $__DOCUMENT_ROOT) {
    $__REQUEST_LOCATION = $__DIR . str_replace($__DIR, '', $__DOCUMENT_ROOT . $__REQUEST_URI);
}

spl_autoload_register(function ($request) use ($__REQUEST_LOCATION, $__REQUEST_URI, $__DOCUMENT_ROOT, $__DIR) {
    $request = $__DIR . '/Classes/' . ltrim(str_replace("\\", "/", $request), '/') . '.php';
    if (file_exists($request)) {
        @require_once $request . '';
    } else {
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
} else if ($file = IndexFiles($__REQUEST_LOCATION)) {

    if (substr(__REQUEST, -1) !== '/') {
        /*A 301 redirect means that the page has permanently moved to a new location.
        A 302 redirect means that the move is only temporary. */
        $uri = "{$__SERVER}{$__REQUEST_URI}/" . ($__REQUEST_QUERY_STRING !== '' ? "?{$__REQUEST_QUERY_STRING}" : '');
        header("Location: $uri", true, 302);
        exit();
    }

    ob_start();
    include $file . '';
    ob_end_flush();
    return true;
} else {
    echo "File Not Found | Error 404";

    return true;
}

function IndexFiles($request_location, $extensions = ['html', 'php'])
{
    $extension_implode = implode(',', $extensions);
    $request_location = rtrim($request_location, '/');
    $res = glob("$request_location/index.{{$extension_implode}}", GLOB_BRACE);
    return count($res) > 0 ? $res[0] : false;
}