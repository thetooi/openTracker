<?php

class Torrent {

    private $id;
    private $_vars = array();

    function __construct($id = "") {
        if ($id != "") {
            $this->id = $id;

            $db = new DB("torrents");
            $db->select("torrent_id = '" . $this->id . "'");
            $db->nextRecord();

            foreach ($db->record as $name => $value)
                $this->__set($name, $value);
        }
    }

    function __set($name, $value) {
        $this->_vars[str_replace("torrent_", "", $name)] = $value;
    }

    function __get($name) {
        return $this->_vars[$name];
    }

    function category() {
        $db = new DB("categories");
        $db->select("category_id = '" . $this->_vars['category'] . "'");
        $db->nextRecord();
        return $db->category_icon;
    }
}

?>
