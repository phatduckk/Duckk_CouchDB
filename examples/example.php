<?php
/**
 * Currently all examples are in 1 file cuz this thing's nowhere near ready and
 * updating multiple files would be fucking annoying.
 *
 * Eventually this will be split into multiple files... each hilighting a different feature
 */
require_once realpath(dirname(__FILE__)) . '/inc.php';
require_once('Duckk/CouchDB.php');
require_once('Duckk/CouchDB/Util.php');

$couchdb   = new Duckk_CouchDB();
$randomDoc = "testing_" . substr(md5(microtime(true)), 0, 8);

echo "------------ example doc put (id = $randomDoc) -------------------\n";
$doc = new Duckk_CouchDB_Document();
$doc->_id = $randomDoc;
print_r($couchdb->putDocument('arin', $doc));

echo "------------ test copying  -------------------\n";
print_r(
    $couchdb->copyDocument(
        'arin', "testing_16bd3b77",
        "testing_b3307897copy",
        "12-2566465478", "1-1053805331"
    )
);

exit;

echo "------------ delete the example doc ($randomDoc) -------------------\n";
print_r($couchdb->deleteDocument('arin', $doc->_id));

exit;

echo "------------ get my test document --------------\n";

try {
    print_r($couchdb->getDocument('arin', 'booya3'));
} catch(Exception $e) {
    print_r($e);
}

echo "------------LIST OF DATABASES---------------\n";
print_r($couchdb->getDatabases());


echo "------------ get rev info for my test document --------------\n";
print_r($couchdb->getDocumentRevisionList('arin', 'booya'));

echo "------------ get rev info for my test document --------------\n";
print_r($couchdb->getDocumentRevisionInfo('arin', 'booya'));


exit;


$randomDBName = 'testing' . md5(microtime(true));
echo "------------CREATE A DB NAMED $randomDBName --------------\n";
var_dump($couchdb->createDatabase("$randomDBName"));
echo "------------TRY TO CREATE $randomDBName again --------------\n";
try {
    $couchdb->createDatabase($randomDBName);
} catch (Exception $e) {
    print_r($e);
}

echo "------------Compact $randomDBName --------------\n";
var_dump($couchdb->compactDatabase("$randomDBName"));

echo "------------ Get info for $randomDBName --------------\n";
print_r($couchdb->getDatabaseInfo($randomDBName));

echo "------------DELETE A DB NAMED $randomDBName --------------\n";
var_dump($couchdb->deleteDatabase($randomDBName));
echo "------------TRY TO DELETE $randomDBName again --------------\n";
try {
    $couchdb->deleteDatabase($randomDBName);
} catch (Exception $e) {
    print_r($e);
}

echo "------------ Get info for $randomDBName After deleting it --------------\n";
try {
    print_r($couchdb->getDatabaseInfo($randomDBName));
} catch (Exception $e) {
    print_r($e);
}

?>