<?php

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
