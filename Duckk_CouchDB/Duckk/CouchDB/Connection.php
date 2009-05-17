<?php

require_once 'Duckk/SimpleHTTP/KeepAlive.php';

class Duckk_CouchDB_Connection extends Duckk_SimpleHTTP_KeepAlive
{
    /**
     * Connection defaults
     */
    const DEFAULT_HOST      = 'localhost';
    const DEFAULT_PORT      = 5984;
    const DEFAULT_KEEPALIVE = 100;

    /**
     * Constructor
     *
     * @param string $host The CouchDB server's hostname or IP
     * @param int    $port The CouchDB server's port
     *
     * @return void
     */
    public function __construct($host = self::DEFAULT_HOST, $port = self::DEFAULT_PORT,
        $keepalive = self::DEFAULT_KEEPALIVE)
    {
        parent::__construct($host, $port, $keepalive);
    }

    /**
     * Make an HTTP GET request against a CouchDB server
     *
     * @param string $uri The URI to request
     *
     * @return mixed An unserialized version of the JSON response from CouchDB
     */
    public function get($uri)
    {
        $resp = parent::get($uri);
        $json = $this->getResponseBody();

        return json_decode($json);
    }

    public function post($uri, $body = null, $contentType = null)
    {
        if ($contentType) {
            $this->addHeader('Content-Type', $contentType);
        }

        $resp = parent::post($uri, $body);
        $json = $this->getResponseBody();

        return json_decode($json);
    }

    public function put($uri, $body = null, $contentType = null)
    {
        if ($contentType) {
            $this->addHeader('Content-Type', $contentType);
        }

        $resp = parent::put($uri, $body);
        $json = $this->getResponseBody();

        return json_decode($json);
    }

    public function delete($uri, $body = null)
    {
        $resp = parent::delete($uri);
        $json = $this->getResponseBody();

        return json_decode($json);
    }
}


?>