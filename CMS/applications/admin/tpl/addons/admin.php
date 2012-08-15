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

$app = new Addon($this->addon);

try {

    if (!$app->hasAdmin())
        throw new Exception("no admin found");

    include(PATH_APPLICATIONS . $this->addon . "/admin/main.php");
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
