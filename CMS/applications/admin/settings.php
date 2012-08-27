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


$this->setSidebar(true);

$this->menu["System"] = page("admin", "settings", "system");
$this->menu["Time"] = page("admin", "settings", "time");
$this->menu["Members"] = page("admin", "settings", "members");
$this->menu["Cleanup"] = page("admin", "settings", "cleanup");

try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("z"))
        throw new Exception("Access denied");

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/settings/");

    $action = isset($this->args["var_a"]) ? $this->args['var_a'] : "";

    switch ($action) {
        default:
            $tpl->loadFile("system.php");
            $this->setTitle("System Settings");
            break;

        case 'time':
            $tpl->loadFile("time.php");
            $this->setTitle("Time Settings");
            break;

        case 'members':
            $tpl->loadFile("members.php");
            $this->setTitle("Members Settings");
            break;

        case 'cleanup':
            $tpl->loadFile("cleanup.php");
            $this->setTitle("Cleanup Settings");
            break;
    }
    $tpl->build();
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
