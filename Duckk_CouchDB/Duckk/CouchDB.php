<?php

require_once 'Duckk/CouchDB/Connection.php';
require_once 'Duckk/CouchDB/Util.php';

class Duckk_CouchDB
{
    private $connection = null;

    public function __construct()
    {
        $this->connection = new Duckk_CouchDB_Connection();
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
     * @param bool $returnSimpleStatus Whether to get the raw response
     *
     * @return array The status of the
     */
    public function createDatabase($database, $returnCouchsResponse = false)
    {
        $status = $this->connection->put(
            Duckk_CouchDB_Util::makeDatabaseURI($database)
        );

        // return the unserialized json from couch
        if ($returnCouchsResponse) {
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

    public function deleteDatabase($database, $returnCouchsResponse = false)
    {
        $status = $this->connection->delete(
            Duckk_CouchDB_Util::makeDatabaseURI($database)
        );

        // return the unserialized json from couch
        if ($returnCouchsResponse) {
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