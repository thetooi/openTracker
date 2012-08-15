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

/**
 * filename index.php
 * 
 * @author Wuild
 * @package openTracker
 */
session_start();

include("init.php");

$framework = new Main(START_APP);
$framework->build();
?>
