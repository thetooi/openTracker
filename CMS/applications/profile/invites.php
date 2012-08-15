<?php

/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Wuild
 * @package openTracker
 */

if(!defined("INCLUDED"))
    die("Access denied");

try {

    $this->setSidebar(true);

    $action = isset($this->args['var_a']) ? $this->args['var_a'] : "";
    $tpl = new Template(PATH_APPLICATIONS . "profile/tpl/");
    switch ($action) {
        default:
            echo "<h4>" . _t("Invites") . "</h4><br />";

            $tpl->build("invites.php");
            break;
        case 'send':
            $tpl->build("invites-send.php");
            break;

        case 'delete':
            $id = $_GET['id'];
            $db = new DB("users");
            $db->select("user_id = '" . $id . "' AND user_status = 1 AND user_invited = '" . USER_ID . "'");
            if (!$db->numRows())
                throw new Exception("user not found");

            $db->delete("user_id = '" . $id . "' AND user_status = 1 AND user_invited = '" . USER_ID . "'");
            header("location: " . page("profile", "invites"));
            break;
    }
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>