<?php
require_once realpath(dirname(__FILE__)) . '/inc.php';
require_once 'Duckk/CouchDB.php';
require_once 'Duckk/CouchDB/Util.php';
require_once 'Duckk/CouchDB/DesignDocument.php';

// Random setup
$couchdb  = new Duckk_CouchDB();
$random   = substr(md5(microtime()), 0, 4);
$database = "employees-{$random}"; // avoid name collissions w/ the md5 suffix

// create an employee db
$db = $couchdb->createDatabase($database);
p("created database: $database", $db);

// create a document for employee named "Arin Sarkissian"
$arin = new Duckk_CouchDB_Document();
$arin->_id       = "employee1"; // you MUST specify an id
$arin->firstName = "Arin";
$arin->lastName  = "Sarkissian";
$arin->title     = "Software Engineer";
$arin->salary    = 2500000;     // i wish
$arin->phone     = "(900) 976-1212";

$putResult = $couchdb->putDocument($database, $arin);
p("Put employee Arin Sarkissian", $putResult);

// create another employee: John Doe
$john = new Duckk_CouchDB_Document();
$john->_id       = "employee2"; // you MUST specify an id
$john->firstName = "John";
$john->lastName  = "Doe";
$john->phone     = "(900) 555-1212";
$john->title     = "CTO";
$john->salary    = 850000;

$putResult = $couchdb->putDocument($database, $john);
p("Put employee John Doe", $putResult);

// GET the document for Arin Sarkissian
p("Get Arin's Data", $couchdb->getDocument($database, 'employee1'));

// change his job title and save the document again
$arin->title = 'Sr Software Engineer';
$putResult   = $couchdb->putDocument($database, $arin);
p("Get Arin's Data after title change", $couchdb->getDocument($database, 'employee1'));

// now create a view
$designDoc = new Duckk_CouchDB_DesignDocument();
$designDoc->setId('empData');
$designDoc->addView('all', 'function(doc) { emit(null, doc.salary); }');
$designDoc->addView('totalPayroll',
    'function(doc) { emit("salary", doc.salary); }',
    'function(name, salary) { return sum(salary) }'
);

// PUT the view
$resp = $couchdb->putDocument($database, $designDoc);
p("PUT the design document", $resp);

// run the "all" view we just put
$viewResult = $couchdb->getDocument($database, $designDoc->_id . '/_view/all');
p("Result of the 'ALL' view", $viewResult);

// run the "totalPayroll" view we just put
$viewResult = $couchdb->getDocument($database, $designDoc->_id . '/_view/totalPayroll');
p("Result of the 'totalPayroll' view", $viewResult);

// clean up after ourselves and delete the DB
$delete = $couchdb->deleteDatabase($database);
p("deleted database: $database", $db);

?>