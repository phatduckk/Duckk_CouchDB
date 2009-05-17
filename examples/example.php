<?php
/**
 * Currently all examples are in 1 file cuz this thing's nowhere near ready and
 * updating multiple files would be fucking annoying.
 *
 * Eventually this will be split into multiple files... each hilighting a different feature
 */
require_once('./inc.php');
require_once('Duckk/CouchDB.php');
require_once('Duckk/CouchDB/Util.php');

print_r(Duckk_CouchDB_Util::getAttachmentInfo('/Users/arin/Downloads/SolrUpdate.php'));
exit;

$couchdb = new Duckk_CouchDB();

$doc = new Duckk_CouchDB_Document();
$doc->_id = 'abcdddd' . md5(time());
$doc->_rev = md5(time() . 'arin');
$doc->name = 'arin';
print_r($couchdb->postDocument('arin', $doc, true));

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