<?php

/**
 * filename library/Pref.php
 * 
 * @author Wuild
 * @package openTracker
 */
class Pref {

    /**
     * Pref target
     * @var string
     */
    private $target;

    /**
     * Pref returned data
     * @var array
     */
    public $_vars = array();

    /**
     * Construct the pref class with a pref target.
     * @param string $target 
     */
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

    /**
     * Store a variable in the class
     * @param string $name
     * @param string $value 
     */
    function __set($name, $value) {
        $this->_vars[$name] = $value;
    }

    /**
     * Return a stored variable.
     * @param string $name
     * @return string
     */
    function __get($name) {
        return $this->_vars[$name];
    }

    /**
     * Update the pref values on the selected target. 
     */
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
