<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('EXAMPLE_PATH', dirname(__FILE__));

$parts = array(
    realpath(EXAMPLE_PATH . '/../Duckk_CouchDB'),
    realpath(EXAMPLE_PATH . '/../../Duckk_SimpleHTTP/Duckk_SimpleHTTP'),
);

set_include_path(implode(PATH_SEPARATOR, $parts));

function p($label, $data) {
    echo "--------------------- $label ---------------------\n";
    print_r($data);
    echo "\n\n";
}
?>