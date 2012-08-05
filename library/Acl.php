<?php

/**
 * @author Wuild
 */

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

    /**
     *  Gather all the info on the selected user id
     * @param string $id 
     */
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

    /**
     *  Set a user variable inside the Acl
     * @param string $name
     * @param string $value 
     */
    function __set($name, $value) {
        $this->_user[str_replace("user_", "", $name)] = $value;
    }

    /**
     * Get the selected users avatar.
     * @return string 
     */
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

    /**
     * Get a user variable inside the Acl
     * @param type $name
     * @return string variable 
     */
    function __get($name) {
        if (isset($this->_user[$name]))
            return $this->_user[$name];
        else
            return "Data " . $name . " not found";
    }

    /**
     * Get the amount of invites
     * @return int
     */
    function invites() {
        return (int) $this->_user['invites'];
    }

    /**
     * Get the amount of uploaded data
     * @return type 
     */
    function uploaded() {
        return bytes($this->_user['uploaded']);
    }

    /**
     * Get the amount of downloaded data
     * @return type 
     */
    function downloaded() {
        return bytes($this->_user['downloaded']);
    }

    /**
     * Get the amount of currently seeding torrents
     * @return int 
     */
    function seeding() {
        $db = new DB;
        $db->query("SELECT COUNT(peer_id) as seeding FROM {PREFIX}peers WHERE peer_seeder = 1 AND peer_userid = '" . $this->id . "'");
        $db->nextRecord();
        return $db->seeding;
    }

    /**
     * Get the amount of currently leeching torrents
     * @return int
     */
    function leeching() {
        $db = new DB;
        $db->query("SELECT COUNT(peer_id) as leeching FROM {PREFIX}peers WHERE peer_seeder = 0 AND peer_userid = '" . $this->id . "'");
        $db->nextRecord();
        return $db->leeching;
    }

    /**
     * Generate a new passkey 
     */
    function newPasskey() {
        $passkey = md5(uniqid(true));
        $db = new DB("users");
        $db->setColPrefix("user_");
        $db->passkey = $passkey;
        $db->update("user_id = '" . $this->id . "'");
        $this->__set("passkey", $passkey);
    }

    /**
     * Get the access level of the selected user
     * @param string $characters
     * @return boolean 
     */
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

    /**
     * Calculate the ratio
     * @return string
     * @throws Exception 
     */
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
