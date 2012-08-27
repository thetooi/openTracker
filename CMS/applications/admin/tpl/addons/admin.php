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

    
    if(!file_exists(PATH_APPLICATIONS . $this->addon . "/admin/".$this->file.".php"))
            throw new Exception("Admin file not found");
    $tpl = new Template(PATH_APPLICATIONS . $this->addon . "/admin/");
    $tpl->build($this->file.".php");
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
