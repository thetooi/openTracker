<?php

class Pref {

    private $target;
    public $_vars = array();

    function __construct($target) {
        $this->target = $target;
        $db = new DB("pref");
        $db->select("pref_target = '" . $db->escape($target) . "'");
        while ($db->nextRecord()) {
            if ($db->pref_value == "0")
                $value = false;
            else if ($db->pref_value == "1")
                $value = true;
            else
                $value = $db->pref_value;

            $this->__set($db->pref_name, $value);
        }
    }

    function __set($name, $value) {
        $this->_vars[$name] = $value;
    }

    function __get($name) {
        return $this->_vars[$name];
    }

    function update() {
        $db = new DB("pref");
        $db->setColPrefix("pref_");
        foreach ($this->_vars as $name => $value) {
            $db->value = $value;
            $db->update("pref_name = '" . $name . "' AND pref_target = '" . $db->escape($this->target) . "'");
        }
    }

}

?>
