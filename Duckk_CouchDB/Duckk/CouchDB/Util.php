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
     * Get the info we need for a document attachment
     *
     * Please NOTE: I couldn't get the FileInfo module to install. Dunno know why PECL
     * wasn't cooperating... probabaly cuz it's jank.  So, this is a decent attempt at
     * getting the right file info till I figure that shit out. So, getting the info
     * for file system paths may or may not work for you. It will not work on Windows
     * cuz I'm using a system call hack as a stop-gap for now (verified on OS X).
     *
     * @param string $pathToFile File system path or HTTP URI to a file
     *
     * @return array The necessary file info in an associative array
     */
    static public function getAttachmentInfo($pathToFile)
    {
        $fileInfo = array(
            'content_type' => null,
            'data'         => null,
            'basename'     => basename($pathToFile)
        );

        if (strpos($pathToFile, 'http') === 0) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $pathToFile);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $fileInfo['data']         = curl_exec($ch);
            $fileInfo['content_type'] = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        } else {
            $cmd = 'file --mime -b ' . escapeshellarg($pathToFile);

            $fileInfo['content_type'] = trim(`$cmd`);
            $fileInfo['data']         = file_get_contents($pathToFile);
        }

        if (strstr($fileInfo['content_type'], ';')) {
            list($fileInfo['content_type'], ) = explode(';', $fileInfo['content_type']);
        }

        // TODO handle errors for http and fs

        return $fileInfo;
    }
}

?>