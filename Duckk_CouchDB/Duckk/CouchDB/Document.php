<?php

class Duckk_CouchDB_Document
{
    /**
     * The ID of the document
     *
     * @var string
     */
    public $_id;

    /**
     * The revision of the document
     *
     * @var string
     */
    public $_rev;

    /**
     * Document attachments
     *
     * @var array
     */
    public $_attachments = array();

    /**
     * Add an attachment to the document
     *
     * @param string $pathToFile
     * @param string $attachmentName
     *
     * @return bool
     */
    public function addAttachment($pathToFile, $attachmentName = null)
    {

    }

    public function getAttchment($attachmentName)
    {

    }
}

?>