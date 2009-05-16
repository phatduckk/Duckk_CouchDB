<?php

require_once('./inc.php');
require_once('Duckk/CouchDB.php');

$couchdb = new Duckk_CouchDB();
print_r($couchdb->getDatabases());

try {
    $couchdb->createDatabase('arin3');
} catch (Exception $e) {
    print_r($e);
}


$couchdb->deleteDatabase('arin3888');

?>