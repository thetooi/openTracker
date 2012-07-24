<?php
session_start();

include("init.php");
$framework = new Main(START_APP);
$framework->build();
?>
