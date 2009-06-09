<?php

require_once 'Duckk/CouchDB/Document.php';

/**
 * Design Document
 *
 */
class Duckk_CouchDB_DesignDocument extends Duckk_CouchDB_Document
{
    /**
     * Default view language
     */
    const DEFAULT_LANGUAGE = 'javascript';

    /**
     * Special uri for a temporary view
     */
    const TEMP_VIEW = '_temp_view';

    /**
     * The language for the view
     *
     * @var string
     */
    public $language = self::DEFAULT_LANGUAGE;

    /**
     * The views
     *
     * @var array
     */
    public $views = array();
    
    /**
     * Whether this is a temporary view or not
     */
    public $isTemporary = false;

    /**
     * Add a view
     *
     * @param string $name   The name of the view
     * @param string $map    The map function
     * @param string $reduce The reduce function
     */
    public function addView($name, $map, $reduce = null)
    {
        $view = array('map' => $map);

        if ($reduce) {
            $view['reduce'] = $reduce;
        }

        $this->views[$name] = $view;
    }

    /**
     * Convenience method for setting the id
     *
     * Let's you not bother typing the _design/ prefix
     *
     * @param string $id The ID
     *
     * @return string The ID
     */
    public function setId($id)
    {
        if (strpos($id, '_design/') !== 0) {
            $id = "_design/{$id}";
        }

        $this->_id = $id;

        return $this->_id;
    }
}

?>