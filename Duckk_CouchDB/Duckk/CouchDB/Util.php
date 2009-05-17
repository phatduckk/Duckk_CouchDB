<?php

class Duckk_CouchDB_Util
{
    /**
     * Check to see if a string is a valid DB name
     *
     * from: http://wiki.apache.org/couchdb/HTTP_database_API
     *  A database must be named with all lowercase characters (a-z),
     *  digits (0-9), or any of the _$()+-/ characters and must end
     *  with a slash in the URL. The name has to start with characters.
     *
     * @param string $database The string to validate
     *
     * @return bool Whether the string is a valid db name or not
     */
    static public function isValidDatabaseName($database)
    {
        return preg_match('/[a-z][a-z0-9_\$,\+\-\//]*\/$/');
    }

    /**
     * "clean" a string and turn it into a valid database name
     *
     * This function doesn't guarantee a valid DB name. It mostly
     * just "fixes" the / character usage. Makes sure we don't have a /
     * at the begining and ensure that we do have one on the end
     *
     * @param string $database The database name to clean
     *
     * @return string The result of the cleaning
     */
    static public function cleanDatabaseName($database)
    {
        return trim($database, '/') . '/';
    }

    /**
     * Crate a URI to a DB from it's name
     *
     * @param string $database The name of the DB
     *
     * @return string The URI
     */
    static public function makeDatabaseURI($database)
    {
        return '/' . self::cleanDatabaseName($database);
    }

    /**
     * Get the info we need
     */
    static public function getAttachmentInfo($pathToFile, $useCurl = false)
    {
        $fileInfo = array(
            'content_type' => null,
            'data'         => null,
            'basename'     => basename($pathToFile)
        );

        if ($useCurl || strpos($pathToFile, 'http') === 0) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $pathToFile);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // TODO throw an exception upon curl errors

            $fileInfo['data']         = curl_exec($ch);
            $fileInfo['content_type'] = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        } else {
            $arg = escapeshellarg($pathToFile);

            $fileInfo['content_type'] = `file -I -b $arg`;
            $fileInfo['data']         = file_get_contents($pathToFile);
        }


        $fileInfo['data'] = base64_encode($fileInfo['data']);

        if (strstr($fileInfo['content_type'], ';')) {
            list($fileInfo['content_type'], ) = explode(';', $fileInfo['content_type']);
        }

        return $fileInfo;
    }
}

?>