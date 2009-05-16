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
     * @param bool   $returnCouchsResponse Whether you want the response from Couch or a
     *  simplified return (aka: true). Set this to TRUE to get the raw Cuch response
     *
     * @return mixed If $returnCouchsResponse === true then you'll get the unserialized
     *  JSON response from Couch. Otherwise you'll get TRUE on success or an exception
     *  upon failure
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

    /**
     * Delete a database
     *
     * @param string $database The name of the DB
     *
     * @param bool   $returnCouchsResponse Whether you want the response from Couch or a
     *  simplified return (aka: true). Set this to TRUE to get the raw Cuch response
     *
     * @return mixed If $returnCouchsResponse === true then you'll get the unserialized
     *  JSON response from Couch. Otherwise you'll get TRUE on success or an exception
     *  upon failure
     */
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