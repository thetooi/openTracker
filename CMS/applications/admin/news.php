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

$this->setTitle("Admin - News");
$this->setSidebar(true);
try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("x"))
        throw new Exception("Access denied");

    $this->menu["News"] = page("admin", "news");
    $this->menu["Compose news"] = page("admin", "news", "compose");
    
    $action = isset($this->args["var_a"]) ? $this->args['var_a'] : "";

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/news/");
    switch ($action) {
        default:
            $tpl->loadFile("main.php");
            break;

        case 'edit':
            $tpl->loadFile("edit.php");
            break;

        case 'delete':
            $tpl->loadFile("delete.php");
            break;

        case 'compose':
            $tpl->loadFile("compose.php");
            break;
    }
    $tpl->build();
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>

