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

?>

<link rel="stylesheet" href="templates/default/css/style.css" />
<?php
if ($this->sidebar) {
    ?>
    <link rel="stylesheet" href="templates/default/css/sidebar.css" />
    <?php
} else if ($this->login) {
    ?>
    <link rel="stylesheet" href="templates/default/css/login.css" />
    <?php
}
?>
<script src='javascript/jquery-bbedit.js' type='text/javascript' ></script>
<script src='javascript/jquery-tipsy.js' type='text/javascript' ></script>
<script src='javascript/custom.js' type='text/javascript' ></script>