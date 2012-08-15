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

header("Content-type: text/javascript");
include("../init.php");

$fw = new Main;
$fw->loadConfig("system.php");

?>
//Some vars translated from php to JS

<?php
if (isset($_GET['app'])) {
    $app = str_replace("/", "", $_GET['app']);
    ?>
    var PATH_APP = "<?php echo str_replace(PATH_ROOT, "", PATH_APPLICATIONS . $app . "/"); ?>";
    <?php
}
?>var PHP_SITE_LIVE = <?php echo ($fw->configs['system']['live']) ? "true" : "false"; ?>;
