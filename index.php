<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
ini_set('html_errors', true);

defined('ROUTES_DIR') or define('ROUTES_DIR', __DIR__);

spl_autoload_register(function ($classname) {
    $filename = sprintf('%s/libs/%s.php', 
        ROUTES_DIR,
        rawurlencode($classname)
    );
    
    if (file_exists($filename)) {
        include_once($filename);
    }
});
