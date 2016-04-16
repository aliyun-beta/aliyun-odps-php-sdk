<?php

require_once(__DIR__ . '/src/ODPS/ProtoBuffer/PBMessage.php');
require_once(__DIR__ . '/src/ODPS/ProtoBuffer/PBTypes.php');
require_once __DIR__ . '/Config.php';

/**
 * Load class file aromatically
 */
spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});