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

$this->setTitle("Admin - Support");
$this->setSidebar(true);
try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("x"))
        throw new Exception("Access denied");

    $action = isset($this->args["var_a"]) ? $this->args['var_a'] : "";

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/support/");
    switch ($action) {
        default:
            $tpl->loadFile("main.php");
            break;
        case 'view':
            $tpl->loadFile("view.php");
            break;
        case 'delete':
            $tpl->loadFile("delete.php");
            break;
    }
    $tpl->build();
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>

