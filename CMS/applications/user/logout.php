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

setcookie(COOKIE_PREFIX . "user", "", time() - 7200, "/", "", "0");
setcookie(COOKIE_PREFIX . "lastvisit", "", time() - 7200, "/", "", "0");

header("location: " . page(START_APP));
?>
