<?php

require_once __DIR__ . "/ODPS/Core/TestBase.php";
//require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../autoload.php';

function outputMsg($msg)
{
    fwrite(STDERR, print_r($msg));
}

if (PHP_INT_SIZE !== 8) {
    throw new \ODPS\Core\OdpsException("ODPS php sdk can only works on php64bit.");
}
