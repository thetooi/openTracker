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

$this->setTitle("Login");

$tpl = new Template($this->path . "tpl/");
$tpl->build("login.php");

if (isset($_POST['login'])) {
    try {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        $db = new DB("users");
        $db->setColPrefix("user_");

        $username = isset($_POST['user_username']) ? $_POST['user_username'] : "";
        $password = isset($_POST['user_password']) ? $_POST['user_password'] : "";

        $username = $db->escape($username);
        $password = $db->escape($password);

        if (empty($username) || empty($password))
            throw new Exception("Wrong username or password");

        $db->select("user_name = '" . $username . "'");

        if (!$db->numRows())
            throw new Exception("Wrong username or password");


        $db->nextRecord();

        switch ($db->status) {
            case 0:
                throw new Exception("Your account is not activated yet. please check your junkbox for the confirmation email");
                break;
            case 1:
                throw new Exception("Your account is not activated yet. please check your junkbox for the confirmation email");
                break;

            case 3:
                throw new Exception("This account has been locked.");
                break;
        }

        $real_pass = $db->password;
        $secret = $db->password_secret;
        if (md5($secret . $password . $secret) != $real_pass)
            throw new Exception("Wrong username or password");

        if (isset($_POST['remember']))
            $cookie_exp = time() + 31536000;
        else
            $cookie_exp = time() + 86400;
        $cookie_value = $db->id . "." . md5("!" . $db->id . md5("!" . md5($db->password_secret . $password . $db->password_secret) . md5($_SERVER['REMOTE_ADDR'])));

        $db->ip = $_SERVER['REMOTE_ADDR'];
        $db->last_login = time();
        $db->update("user_name = '" . $username . "'");

        setcookie(COOKIE_PREFIX . "user", $cookie_value, $cookie_exp, "/", "", "0");
        header("Location: " . page(START_APP));
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>