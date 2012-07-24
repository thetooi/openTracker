<?php

$this->setTitle("Account confirmation");

try {
    if (!isset($_GET['key']))
        throw new Exception("Missing registration key");


    $db = new DB("users");
    $db->select("user_password_secret = '" . $db->escape($_GET['key']) . "' AND user_status = '1'");

    if (!$db->numRows())
        throw new Exception("Could not find user.");

    $db->nextRecord();
    $uid = $db->user_id;

    $cookie_exp = time() + 86400;

    $cookie_value = $db->user_id . "." . md5("!" . $db->user_id . md5("!" . $db->user_password . md5($_SERVER['REMOTE_ADDR'])));
    $db = new DB("users");
    $db->user_status = 4;
    $db->user_ip = $_SERVER['REMOTE_ADDR'];
    $db->user_last_login = time();
    $db->update("user_id = '" . $uid . "'");

    setcookie(COOKIE_PREFIX . "user", $cookie_value, $cookie_exp, "/", "", "0");
    header("Location: " . page(START_APP));
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
