<?php

require_once 'Duckk/CouchDB/Exception.php';

class Duckk_CouchDB_Exception_DatabaseMissing extends Duckk_CouchDB_Exception
{
    public $error;
    
    public function __construct(stdClass $status)
    {        
        parent::__construct($status->reason, self::CODE_DB_MISSING);
        $this->error = $status->error;
    }
    
    public function getError()
    {
        return $this->error;
    }
}

?>