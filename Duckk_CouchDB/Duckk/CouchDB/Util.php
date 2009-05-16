<?php

public class Duckk_CouchDB_Util
{
    static public function isValidDatabaseName($databaseName)
    {
        return ($databaseName == urlencode(strtolower($databaseName));
    }
}

?>