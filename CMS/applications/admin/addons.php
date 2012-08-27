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

$this->setTitle("Admin - Addons");
$this->setSidebar(true);
try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("z"))
        throw new Exception("Access denied");

    $action = isset($this->args["var_a"]) ? $this->args['var_a'] : "";

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/addons/");
    switch ($action) {
        default:
            $tpl->addon = (isset($this->args['var_b'])) ? $this->args['var_b'] : false;
            $tpl->loadFile("main.php");
            break;

        case 'admin':
            $tpl->addon = (isset($this->args['var_b'])) ? $this->args['var_b'] : false;
            $tpl->file = (isset($this->args['var_c'])) ? $this->args['var_c'] : "main";
            $tpl->loadFile("admin.php");
            break;

        case 'install':
            $tpl->addon = (isset($this->args['var_b'])) ? $this->args['var_b'] : false;
            $tpl->loadFile("install.php");
            break;

        case 'edit':
            $tpl->addon = (isset($this->args['var_b'])) ? $this->args['var_b'] : false;
            $tpl->loadFile("edit.php");
            break;

        case 'export':
            $tpl->addon = (isset($this->args['var_b'])) ? $this->args['var_b'] : false;
            $tpl->loadFile("export.php");
            break;

        case 'uninstall':
            $tpl->loadFile("delete.php");
            break;
    }
    $tpl->build();
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>

