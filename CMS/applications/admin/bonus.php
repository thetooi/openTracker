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
if (!defined("INCLUDED"))
    die("Access denied");

try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("x"))
        throw new Exception("Access denied");

    $this->setTitle("Admin - Bonus store");
    $this->menu["Bonus Store"] = page("admin", "bonus");
    $this->menu["Add new item"] = page("admin", "bonus", "create");

    $action = isset($this->args["var_a"]) ? $this->args['var_a'] : "";

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/bonus/");
    switch ($action) {
        default:
            $tpl->build("main.php");
            break;

        case 'create':
            $tpl->build("create.php");
            break;

        case 'delete':
            $tpl->build("delete.php");
            break;

        case 'edit':
            $tpl->id = ($_GET['id']) ? $_GET['id'] : false;
            $tpl->build("edit.php");
            break;
    }
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
