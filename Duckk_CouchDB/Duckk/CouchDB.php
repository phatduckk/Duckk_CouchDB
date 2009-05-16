<?php

require_once 'Duckk/CouchDB/Connection.php';
require_once 'Duckk/CouchDB/Util.php';

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
        $status = $this->connection->put(
            Duckk_CouchDB_Util::makeDatabaseURI($database)
        );

        // return the unserialized json from couch
        if ($this->returnJsonFromCouch) {
            return $status;
        }

        // return php-ish values

        if (isset($status->ok) && $status->ok == 1) {
            return true;
        } else {
            require_once 'Duckk/CouchDB/Exception/DatabaseExists.php';
            throw new Duckk_CouchDB_Exception_DatabaseExists($status);
        }
    }

    /**
     * Delete a database
     *
     * @param string $database The name of the DB
     */
    public function deleteDatabase($database)
    {
        $status = $this->connection->delete(
            Duckk_CouchDB_Util::makeDatabaseURI($database)
        );

        // return the unserialized json from couch
        if ($this->returnJsonFromCouch) {
            return $status;
        }

        // return php-ish values

        if (isset($status->ok) && $status->ok == 1) {
            return true;
        } else {
            require_once 'Duckk/CouchDB/Exception/DatabaseMissing.php';
            throw new Duckk_CouchDB_Exception_DatabaseMissing($status);
        }
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
        $status = $this->connection->get(
            Duckk_CouchDB_Util::makeDatabaseURI($database)
        );

        if (isset($status->error)) {
            require_once 'Duckk/CouchDB/Exception/DatabaseMissing.php';
            throw new Duckk_CouchDB_Exception_DatabaseMissing($status);
        } else {
            return $status;
        }
    }
}

?>