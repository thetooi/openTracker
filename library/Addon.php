<?php

class Addon {

    private $_addon;
    private $_name;

    function __construct($name) {
        $this->_addon = PATH_APPLICATIONS . $name . "/";
        $this->_name = $name;
    }

    function isInstalled() {
        $db = new DB("addons");
        $db->select("addon_name = '" . $db->escape($this->_name) . "' and addon_installed = '1'");
        if ($db->numRows())
            return true;
    }

    function Access() {
        $acl = new Acl(USER_ID);

        $db = new DB("addons");
        $db->select("addon_name = '" . $db->escape($this->_name) . "' AND addon_installed = '1'");
        $db->nextRecord();
        if ((int) $acl->group < (int) $db->addon_group)
            return false;
        else
            return true;
    }

    function hasAdmin() {
        if (file_exists($this->_addon . "admin/main.php"))
            return true;
    }

    function checkInstall() {
        if (file_exists($this->_addon . "install.php"))
            return true;
    }

    function Install() {
        if ($this->checkInstall()) {
            include($this->_addon . "install.php");
            return true;
        }
    }

}

?>
