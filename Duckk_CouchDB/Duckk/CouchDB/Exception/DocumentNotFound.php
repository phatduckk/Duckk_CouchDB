<?php

require_once 'Duckk/CouchDB/Exception.php';

class Duckk_CouchDB_Exception_DocumentNotFound extends Duckk_CouchDB_Exception
{
    public function __construct($uri, stdClass $status)
    {
        parent::__construct("No document at {$uri}. CouchDB says: {$status->reason}", self::CODE_DOCUMENT_NOT_FOUND);
        $this->error = $status->error;
    }

    public function getError()
    {
        return $this->error;
    }
}

?>