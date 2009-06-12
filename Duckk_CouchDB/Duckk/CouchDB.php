<?php

require_once 'Duckk/CouchDB/Connection.php';
require_once 'Duckk/CouchDB/Util.php';
require_once 'Duckk/CouchDB/Document.php';
require_once 'Duckk/CouchDB/QueryOptions.php';

class Duckk_CouchDB
{
    /**
     * Connection to CouchDB
     *
     * @var Duckk_CouchDB_Connection
     */
    private $connection = null;

    /**
     * Whether the method calls to this class should return the unserialized JSON (an stdClass)
     * from CouchDB or an "interpretted" PHP value.
     *
     * When set to TRUE a successful call to $this->createDatabase('foo') will
     * return an stdClass like:
     *
     * <pre>
     * object(stdClass)#3 (1) {
     *  ["ok"]=>
     *     bool(true)
     * }
     * </pre>
     *
     * When set to FALSE the class will "examine" the response and, in the case of
     * the example above (a successful call), return a bool TRUE.
     *
     * Upon a failed request the class will return the unserialized JSON (an stdClass)
     * from CouchDB if $this->returnJsonFromCouch === TRUE. If $this->returnJsonFromCouch
     * === FALSE then the class will throw an exception instead.
     *
     * @var bool
     */
    public $returnJsonFromCouch = false;

    /**
     * Constructor
     *
     * @param string $host      The CouchDB server's hostname or IP
     * @param int    $port      The CouchDB server's port
     * @param string $keepalive The value for the connection's Keep-Alive request header
     *
     * @return void
     */
    public function __construct($host = Duckk_CouchDB_Connection::DEFAULT_HOST,
        $port = Duckk_CouchDB_Connection::DEFAULT_PORT,
        $keepalive = Duckk_CouchDB_Connection::DEFAULT_KEEPALIVE)
    {
        $this->connection = new Duckk_CouchDB_Connection($host, $port, $keepalive);
    }

    /**
     * Get a list of databases on the connected CouchDB server
     *
     * @return array The names of the db's in an array
     */
    public function getDatabases()
    {
        return $this->connection->get('/_all_dbs');
    }

    /**
     * Create a database
     *
     */
    public function createDatabase($database)
    {
        return $this->connection->put(
            Duckk_CouchDB_Util::makeDatabaseURI($database, null, 'application/json')
        );
    }

    /**
     * Delete a database
     *
     * @param string $database The name of the DB
     */
    public function deleteDatabase($database)
    {
        return $this->connection->delete(
            Duckk_CouchDB_Util::makeDatabaseURI($database)
        );
    }

    /**
     * Run compaction on a DB
     *
     * @param string $database Name of the DB
     *
     * @return stdClass Status of the request
     */
    public function compactDatabase($database)
    {
        $status = $this->connection->post(
            Duckk_CouchDB_Util::makeDatabaseURI($database)
            . '_compact'
        );

        return $status;
    }

    /**
     * Get info about a DB
     *
     * @param string $database The name of the DB
     *
     * @return stdClass
     */
    public function getDatabaseInfo($database)
    {
        return $this->connection->get(
            Duckk_CouchDB_Util::makeDatabaseURI($database)
        );
    }

    /**
     * Get a document
     *
     * This method allows you to get a document and also append an arbitary set of
     * query string paramaters to the request URI
     *
     * @param string $database    The name of the DB the document lives in
     * @param string $documentId  The ID of the document
     * @param array  $queryParams An assoc array of query string parameters
     *
     * @return stdClass
     */
    public function _getDocument($database, $documentId, array $queryParams = array())
    {
        $databaseURI = Duckk_CouchDB_Util::makeDatabaseURI($database);
        $documentURI = $databaseURI . $documentId;
        $queryString = '';

        if (! empty($queryParams)) {
            $qryParts = array();

            foreach ($queryParams as $k => $v) {
                $qryParts[] = urlencode($k) . '=' . urlencode($v);
            }

            $documentURI .= '?' . implode('&', $qryParts);
        }

        return $this->connection->get($documentURI);
    }

    /**
     * Get a document by ID
     *
     * You can optionally get a list of revisions and/or info about each revision.
     *
     * @param string $database        The name of the DB the document lives in
     * @param string $documentId      The ID of the document
     * @param bool   $getRevisionList Get the revision list too?
     * @param bool   $getRevisionInfo Get the revision info too?
     *
     * @return stdClass
     */
    public function getDocument($database, $documentId,
        $getRevisionList = false, $getRevisionInfo = false)
    {
        $qryParams = array();

        if ($getRevisionList) {
            $qryParams['revs'] = 'true';
        }

        if ($getRevisionInfo) {
           $qryParams['revs_info'] = 'true';
        }

        return $this->_getDocument($database, $documentId, $qryParams);
    }

    /**
     * Get a specific revision of a document
     *
     * You can optionally get the revision list as well.
     *
     * From what I can tell you cannot get a doc by revision AND ask for the revision info.
     * Couch doesn't get pissed... it just doesn't include the _revs_info. But it does
     * seem to return the revision list if you ask for it
     *
     * @param string $database        The DB name
     * @param string $documentId      The ID of the document
     * @param string $rev             Which revision to get
     * @param bool   $getRevisionList Fetch the revision list too?
     *
     * @return stdClass The document
     */
    public function getDocumentRevision($database, $documentId, $rev, $getRevisionList = false)
    {
        $qryParams = array('rev' => $rev);

        if ($getRevisionList) {
            $qryParams['revs'] = 'true';
        }

        return $this->_getDocument($database, $documentId, $qryParams);
    }

    /**
     * Get a list of known revisions for a document
     *
     * @param string $database        The DB name
     * @param string $documentId      The ID of the document
     *
     * @return stdClass The document
     */
    public function getDocumentRevisionList($database, $documentId)
    {
        $resp = $this->_getDocument($database, $documentId, array('revs' => 'true'));
        return (isset($resp->_revs))
            ? $resp->_revs
            : null;
    }

    /**
     * Get the revision infomrtaion of a document
     *
     * @param string $database   The DB name
     * @param string $documentId The ID of the document
     *
     * @return stdClass The document
     */
    public function getDocumentRevisionInfo($database, $documentId)
    {
        $resp = $this->_getDocument($database, $documentId, array('revs_info' => 'true'));
        return (isset($resp->_revs_info))
            ? $resp->_revs_info
            : null;
    }

    /**
     * PUT a document.
     *
     * You can use this to "create" or "update" a document. If you're creating a new doc
     * then the server assigned revision will be assigned to $doc->_rev
     *
     * @param string                 $database The database to put the document in to
     * @param Duckk_CouchDB_Document $doc      The document to PUT
     * @param bool                   $batchOK  Whether or not you want to allow batch processing on Couch
     *                                         This should be set to FALSE for all critical data
     *
     * @return stdClass The response from CouchDB
     */
    public function putDocument($database, Duckk_CouchDB_Document &$doc, $batchOK = false)
    {
        if (! $doc->hasRevision()) {
            unset($doc->_rev);
        }

        if (! $doc->hasAttachments()) {
            unset($doc->_attachments);
        }

        $uri  = Duckk_CouchDB_Util::makeDatabaseURI($database)
              . $doc->_id
              . (($batchOK) ? '?batch=ok' : '');

        $json = json_encode($doc);
        $resp = $this->connection->put($uri, $json, 'application/json');

        $doc->_rev = $resp->rev;

        return $resp;
    }

    /**
     * POST a new Document
     *
     * The CouchDB docs pretty much say you shouldn't use this and should use PUt instead.
     * http://wiki.apache.org/couchdb/HTTP_Document_API
     *
     * If you want to create a new Document and are too lazy to try generate a unique
     * yourself, then this will work. So, more or less it's like PUT but soley for creating
     * a NEW document AND CouchDB will assign an ID to the document for you.
     *
     * The class will add the rev and id to the document you passed in
     *
     * @param string                 $database The DB to post the document to
     * @param Duckk_CouchDB_Document $doc      The document to post
     *
     * @return stdClass
     */
    public function postDocument($database, Duckk_CouchDB_Document &$doc)
    {
        $uri  = Duckk_CouchDB_Util::makeDatabaseURI($database);
        $json = json_encode($doc);
        $resp = $this->connection->post($uri, $json, 'application/json');

        $doc->_rev = $resp->rev;
        $doc->_id  = $resp->id;

        return $resp;
    }

    /**
     * Delete a document
     *
     * You can either pass in a db name with the document id, or the db name with an
     * instance of Duckk_CouchDB_Document.
     *
     * If you dont pass in a Duckk_CouchDB_Document for $documentID then you have to
     * pass in the $documentRevision as the 3rd param
     *
     * @param string                        $database         The name of the DB the document is in
     * @param string|Duckk_CouchDB_Document $documentID       The document's ID or the document itself
     * @param string                        $documentRevision The rev of the document to delete
     *
     * @return stdClass
     */
    public function deleteDocument($database, $documentID, $documentRevision = null)
    {
        $uri = null;
        if ($documentID instanceof Duckk_CouchDB_Document) {
            $uri = Duckk_CouchDB_Util::makeDatabaseURI($database)
                 . $documentID->_id
                 . "?rev={$documentID->_rev}";
        } else {
            $uri = Duckk_CouchDB_Util::makeDatabaseURI($database)
                 . $documentID
                 . "?rev={$documentRevision}";
        }

        return $this->connection->delete($uri);
    }

    /**
     * PUT an attachment
     *
     * @param string $database          Name of the DB
     * @param string $documentID        ID of the document
     * @param string $attachmentContent Type Content-Type of the attachment
     * @param string $attachmentData    The data of the attachment
     * @param string $documentRevision  The revision of the document
     *
     * @return stdClass
     */
    public function putAttachment($database, $documentID,
        $attachmentContentType, $attachmentData,
        $documentRevision = null)
    {
        $uri  = Duckk_CouchDB_Util::makeDatabaseURI($database)
              . $documentID
              . (($documentRevision) ? "?rev={$documentRevision}" : '');

        $resp = $this->connection->put(
            $uri,
            base64_encode($attachmentData),
            $attachmentContentType
        );

        return $resp;
    }

    /**
     * Copy a document
     *
     * @param string $sourceDatabase        Name of the database
     * @param string $sourceDocumentId      The ID of the document being copied
     * @param string $destinationDocumentId The copy's document ID
     * @param string $destinationRevision   The revision of the copy
     * @param string $sourceRevision        The revision of the source document
     *
     * @return stdClass
     */
    public function copyDocument($sourceDatabase, $sourceDocumentId,
        $destinationDocumentId, $destinationRevision = null, $sourceRevision = null)
    {
        $uri  = Duckk_CouchDB_Util::makeDatabaseURI($sourceDatabase)
              . $sourceDocumentId
              . (($sourceRevision) ? "?rev={$sourceRevision}" : '');

        $destinationURI = $destinationDocumentId
            . (($destinationRevision) ? "?rev={$destinationRevision}" : '');

        return $this->connection->copy($uri, $destinationURI);
    }

    /**
     * Get a list of all DB's and metadata for a databadse
     *
     * @param string $database Name of the database
     *
     * @return stdClass
     */
    public function getAllDocuments($database, Duckk_CouchDB_QueryOptions $queryOptions = null)
    {
        $uri = Duckk_CouchDB_Util::makeDatabaseURI($database) . '_all_docs';
        $qry = ($queryOptions) ? $queryOptions->getQueryString() : '';

        if ($qry) {
            $uri .= "?{$ary}";
        }

        return $this->connection->get($uri);
    }

    public function getAllDocumentsBySequence($database,
        Duckk_CouchDB_QueryOptions $queryOptions = null)
    {
        $uri = Duckk_CouchDB_Util::makeDatabaseURI($database) . 'all_docs_by_seq';
        $qry = ($queryOptions) ? $queryOptions->getQueryString() : '';

        if ($qry) {
            $uri .= "?{$ary}";
        }

        return $this->connection->get($uri);
    }

    /**
     * PUT a design document
     *
     * @param string                       $database The DB the view belongs to
     * @param Duckk_CouchDB_DesignDocument The view document
     *
     * @return stdClass The unserialized response from CouchDB
     */
    public function putDesignDocument($database, Duckk_CouchDB_DesignDocument &$doc)
    {
        return $this->putDocument($database, $doc);
    }

    /**
     * GET the result of a view
     *
     * @param string $database         Name of the DB
     * @param string $designDocumentID ID of the design document
     * @param string $viewName         The name of the view to run
     *
     * @return stdClass The response from CouchDB
     */
    public function getView($database, $designDocumentID, $viewName)
    {
        $viewName         = preg_replace('/^_view\//', '', trim($viewName, '/'));
        $designDocumentID = preg_replace('/^_design\//', '', trim($designDocumentID, '/'));

        return $this->getDocument($database, "_design/{$designDocumentID}/_view/{$viewName}");
    }

    public function replicate($sourceDBName, $targetDB)
    {
        $data = array(
            'source' => $sourceDBName,
            'target' => $targetDB
        );

        $status = $this->connection->post(
            Duckk_CouchDB_Util::makeDatabaseURI('/_replicate'),
            json_encode($data)
        );

        return $status;
    }
}

?>