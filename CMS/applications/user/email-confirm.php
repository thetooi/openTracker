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

$this->setTitle("Account Confirmation");

try {
    if (!isset($this->args['var_a']) && !isset($this->args['var_b']) && !isset($_GET['email']))
        throw new Exception("access denied");

    $username = $this->args['var_a'];
    $secret = $this->args['var_b'];

    if (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL))
        throw new Exception("Invalid email address");

    $email = $_GET['email'];

    $db = new DB("users");
    $db->select("user_name = '" . $username . "'");
    if (!$db->numRows())
        throw new Exception("Could not find user");

    $db->nextRecord();
    if ($secret != md5($db->user_password_secret))
        throw new Exception("Edit key did not match");

    $db = new DB("users");
    $db->user_email = $email;
    $db->update("user_name = '" . $username . "'");
    header("location: " . page("profile", "edit"));
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
