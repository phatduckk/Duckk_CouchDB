<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$parts = array(
    realpath('../Duckk_CouchDB'),
    realpath('../../Duckk_SimpleHTTP/Duckk_SimpleHTTP'),
);

set_include_path(implode(PATH_SEPARATOR, $parts));
?>