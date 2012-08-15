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

$this->setTitle("Admin - Forum");
$this->addJavascript("sortable.js");
$this->setSidebar(true);
try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("x"))
        throw new Exception("Access denied");

    $this->menu["Forums"] = page("admin", "forum");
    $this->menu["Create forum category"] = page("admin", "forum", "create-category");
    $this->menu["Create forum"] = page("admin", "forum", "create-forum");


    $action = isset($this->args["var_a"]) ? $this->args['var_a'] : "";

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/forum/");
    switch ($action) {
        default:
            $tpl->loadFile("main.php");
            break;

        case 'create-forum':
            $tpl->loadFile("create-forum.php");
            break;

        case 'edit-forum':
            $tpl->loadFile("edit-forum.php");
            break;

        case 'create-category':
            $tpl->loadFile("create-category.php");
            break;

        case 'edit-category':
            $tpl->loadFile("edit-category.php");
            break;

        case 'delete-forum':
            $tpl->loadFile("delete-forum.php");
            break;

        case 'delete-category':
            $tpl->loadFile("delete-category.php");
            break;
    }
    $tpl->build();
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>

