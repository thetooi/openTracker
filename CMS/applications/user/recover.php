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

$this->setTitle("Recover Password");

if (isset($_POST['recover'])) {
    try {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        $email = $_POST['email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new Exception("Invalid email address");

        $db = new DB("users");
        $db->select("user_email = '" . $db->escape($email) . "'");
        if (!$db->numRows())
            throw new Exception("User not found");
        $db->nextRecord();

        $username = $db->user_name;
        $secret = generatePassword(12);
        $password = generatePassword();
        $password_hash = md5($secret . $password . $secret);

        $db = new DB("users");
        $db->user_password_secret = $secret;
        $db->user_password = $password_hash;
        $db->update("user_email = '" . $db->escape($email) . "'");

        $body = "Hey " . $username . "<br />Someone hopefully you have used the password recovery.<br />If you did not do this, please contact the staff as soon as possible.<br /><br /><b>New Password:</b> " . $password;

        sendEmail($email, "Password recovery", $body);
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
} else {
    $tpl = new Template($this->path . "tpl/");
    $tpl->build("recover.php");
}
?>
