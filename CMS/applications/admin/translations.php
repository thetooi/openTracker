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

$this->setTitle("Admin - Settings");
$this->setSidebar(true);
try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("z"))
        throw new Exception("Access denied");

    $this->menu["Languages"] = page("admin", "translations");
    $this->menu["Create language"] = page("admin", "translations", "create");
    $this->menu["Import language"] = page("admin", "translations", "import");

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/translations/");
    $action = isset($this->args["var_a"]) ? $this->args['var_a'] : "";

    switch ($action) {
        default:
            $tpl->loadFile("main.php");
            break;

        case 'edit':
            $tpl->lang_id = (isset($this->args['var_b'])) ? $this->args['var_b'] : 0;
            $tpl->loadFile("edit.php");
            break;

        case 'import':
            $tpl->loadFile("import.php");
            break;

        case 'export':
            $tpl->lang_id = (isset($this->args['var_b'])) ? $this->args['var_b'] : 0;
            $tpl->loadFile("export.php");
            break;

        case 'delete':
            $tpl->loadFile("delete.php");
            break;

        case 'create':
            $tpl->loadFile("create.php");
            break;
    }
    $tpl->build();
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
