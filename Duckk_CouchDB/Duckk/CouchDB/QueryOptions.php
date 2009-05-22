<?php

class Duckk_CouchDB_QueryOptions
{
    public $key;
    public $startkey;
    public $startkey_docid;
    public $endkey;
    public $endkey_docid;
    public $limit;
    public $stale;
    public $descending;
    public $skip;
    public $group;
    public $group_level;
    public $reduce;
    public $include_docs;

    public function getQueryString()
    {
        $members     = get_object_vars($this);
        $qryStrParts = array();

        foreach ($members as $k => $v)
        {
            if (! isset($this->$k) || $v === null) {
                continue;
            }

            if ($k == 'stale') {
                $v = 'ok';
            } else if (is_bool($v)) {
                $v = ($v) ? 'true' : 'false';
            }

            $qryStrParts[$k] = $v;
        }

        return (! empty($qryStrParts))
            ? http_build_query($qryStrParts)
            : null;
    }
}

?>