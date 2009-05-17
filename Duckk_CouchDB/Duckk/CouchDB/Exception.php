<?php

class Duckk_CouchDB_Exception extends Exception
{
    /**
     * Error codes
     */
    const CODE_DB_EXISTS        = 555;
    const CODE_DB_MISSING       = 556;
    const CODE_DOCUMENT_NOT_FOUND = 557;
    const CODE_DOCUMENT_UPDATE_CONFLICT = 558;

    /**
     * The errror from CouchDB
     *
     * @var string
     */
    public $error;

    /**
     * Create an exception based upon CouchDB's response and the type of action
     * we were performing that caused the error
     *
     * @param stdClass $response The unserialized response from CouchDB
     * @param string   $action   The name of the action that caused this
     *
     * @return Duckk_CouchDB_Exception The exception to throw
     */
    static public function factory(stdClass $response, $action)
    {

    }
}

?>