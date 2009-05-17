<?php

require_once 'Duckk/CouchDB/Util.php';

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
     * Add an attachment to the document by file system or HTTP path (URI)
     *
     * We'll try to figure out the name and content type of the attachment for you
     * but you can optionally provide that info.
     *
     * NOTE: I tried installing the Fileinfo module via PECL but it wasn't cooperating.
     * So, I make a decent attempt to grab the right content type etc... but it can be
     * faulty. So if this gives you any trouble use $this->addAttachment() instead.
     *
     * @param string $pathToFile  Path to the file you want to attach
     * @param string $contentType Content-Type of the attachment
     * @param string $name        The name of the attachment (optional)
     *
     * @return array The attachment being sent to CouchDB
     */
    public function addAttachmentByPath($pathToFile, $contentType = null, $name = null)
    {
        $info = Duckk_CouchDB_Util::getAttachmentInfo($pathToFile);

        if ($contentType !== null) {
            $info['content_type'] = $contentType;
        }

        // if $attachmentName is given then use that as opposed to $info['basename']
        if ($name === null) {
            $name = $info['basename'];
        }

        return $this->addAttachment($name, $info['data'], $info['content_type']);
    }

    /**
     * Manually attach a file to the document
     *
     * @param string $name           The name of the attachment
     * @param string $data           The attachment's data
     * @param string $contentType    The content type
     * @param bool   $alreadyEncoded Whether $data is already base64 encoded or not
     *
     * @return array The attachment being sent to CouchDB
     */
    public function addAttachment($name, $data, $contentType, $alreadyEncoded = false)
    {
        $attachment = array(
            'data'         => (($alreadyEncoded) ? $data : base64_encode($data)),
            'content_type' => addslashes($contentType)
        );

        $this->_attachments[$name] = $attachment;

        return $this->_attachments[$name];
    }

    /**
     * See if there's any attachments
     *
     * @return bool
     */
    public function hasAttachments()
    {
        return (! empty($this->_attachments));
    }

    /**
     * See if the document has a revision or not
     *
     * @return bool
     */
    public function hasRevision()
    {
        return ((bool)$this->_rev);
    }
}

?>