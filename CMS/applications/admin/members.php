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

$this->setTitle("Admin - Members");
$this->setSidebar(true);
try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("x"))
        throw new Exception("Access denied");

    $action = isset($this->args["var_a"]) ? $this->args['var_a'] : "";

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/members/");
    switch ($action) {
        default:
            $tpl->loadFile("main.php");
            break;

        case 'edit':
            $tpl->userid = (getID($this->args['var_b'])) ? getID($this->args['var_b']) : 0;
            $tpl->loadFile("edit.php");
            break;

        case 'log':
            $tpl->userid = (getID($this->args['var_b'])) ? getID($this->args['var_b']) : 0;
            $tpl->loadFile("log.php");
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

