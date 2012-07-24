<?php

/*
 * 0 = inactive
 * 1 = pending
 * 3 = locked
 * 4 = active
 */

class Acl {

    private $_user = array();
    private $_group;
    private $_id;

    function __construct($id) {
        $this->id = $id;
        $db = new DB("users");
        $db->setColPrefix("user_");
        $db->select("user_id = '$id'");

        if ($db->numRows()) {
            $db->nextRecord();
            foreach ($db->record as $name => $value) {
                if ($value == "0")
                    $value = false;
                else if ($value == "1")
                    $value = true;

                $this->__set($name, $value);
            }

            $db = new DB("groups");
            $db->setColPrefix("group_");
            $db->select("group_id = '" . $this->_user['group'] . "'");
            $db->nextRecord();

            $this->__set("group_name", $db->name);
            $this->__set("group_access", $db->acl);
        } else {
            $this->__set("id", "0");
            $this->__set("name", "Unknown");
            $this->__set("avatar", "");
        }
    }

    function __set($name, $value) {

        $this->_user[str_replace("user_", "", $name)] = $value;
    }

    function Avatar() {
        if ($this->_user['avatar'] == "") {
            return "images/default_avatar.jpg";
        } else {
            if (file_exists(PATH_AVATARS . $this->_user['avatar']))
                return "images/avatars/" . $this->_user['avatar'];
            else
                return "images/default_avatar.jpg";
        }
    }

    function __get($name) {
        if (isset($this->_user[$name]))
            return $this->_user[$name];
        else
            return "Data " . $name . " not found";
    }

    function invites() {
        return (int) $this->_user['invites'];
    }

    function uploaded() {
        return bytes($this->_user['uploaded']);
    }

    function downloaded() {
        return bytes($this->_user['downloaded']);
    }

    function seeding() {
        $db = new DB;
        $db->query("SELECT COUNT(peer_id) as seeding FROM {PREFIX}peers WHERE peer_seeder = 1 AND peer_userid = '" . $this->id . "'");
        $db->nextRecord();
        return $db->seeding;
    }

    function leeching() {
        $db = new DB;
        $db->query("SELECT COUNT(peer_id) as leeching FROM {PREFIX}peers WHERE peer_seeder = 0 AND peer_userid = '" . $this->id . "'");
        $db->nextRecord();
        return $db->leeching;
    }

    function newPasskey() {
        $passkey = md5(uniqid(true));
        $db = new DB("users");
        $db->setColPrefix("user_");
        $db->passkey = $passkey;
        $db->update("user_id = '" . $this->id . "'");
        $this->__set("passkey", $passkey);
    }

    function Access($characters) {
        $allowed = true;
        $req = str_split($characters);
        foreach ($req AS $char) {
            if (!in_array($char, str_split($this->_user['group_access']))) {
                $allowed = false;
                break;
            }
        }
        if ($allowed)
            return true;
    }

    function ratio() {
        try {
            if ($this->_user['uploaded'] == 0)
                throw new Exception();

            if ($this->_user['downloaded'] == 0)
                throw new Exception();

            return round($this->_user['uploaded'] / $this->_user['downloaded'], 2);
        } Catch (Exception $e) {
            return "--";
        }
    }

}

?>
