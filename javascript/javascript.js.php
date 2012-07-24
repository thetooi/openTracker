<?php
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
