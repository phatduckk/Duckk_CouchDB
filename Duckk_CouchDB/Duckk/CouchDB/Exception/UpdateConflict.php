<?php

require_once 'Duckk/CouchDB/Exception.php';

class Duckk_CouchDB_Exception_UpdateConflict extends Duckk_CouchDB_Exception
{
    public function __construct($uri, stdClass $status)
    {
        parent::__construct("Update conflict at {$uri}. CouchDB says: {$status->reason}", self::CODE_DOCUMENT_UPDATE_CONFLICT);
        $this->error = $status->error;
    }

    public function getError()
    {
        return $this->error;
    }
}

?>