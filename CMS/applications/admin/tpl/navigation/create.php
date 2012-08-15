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

<h4><?php echo _t("Create navigation item") ?></h4>
<?php
$actions = array();

$applications = makefilelist(PATH_APPLICATIONS, ".|..", true, "folders");

foreach ($applications as $name) {
    $files = makefilelist(PATH_APPLICATIONS . $name, ".|..|ajax.php|tpl|index.html|install.php|widget.php", true);
    $actions[$name][] = $files;
}


if (isset($_POST['create'])) {
    try {
        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        if (empty($_POST['title']))
            throw new Exception("Empty title");

        $app = $_POST['application'];
        $action = $_POST['action_' . $app] != "main" ? $_POST['action_' . $app] : "";

        $db = new DB("navigations");
        $db->setColPrefix("navigation_");
        $db->title = $_POST['title'];
        $db->application = $app;
        $db->module = $action;
        $db->lang = $_POST['language'];
        $db->insert();

        header("location: " . page("admin", "navigation"));
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>

<form method="post">
    <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
    <table>
        <tr>
            <td><?php echo _t("Title") ?></td>
            <td><input type="text" name="title"></td>
        </tr>
        <tr>
            <td><?php echo _t("Application") ?></td>
            <td><select id="application" name="application"><?php echo makefileopts($applications); ?></select></td>
        </tr>
        <tr>
            <td><?php echo _t("Action module") ?></td>
            <td>
                <?php
                foreach ($actions as $name => $files) {
                    echo "<select id='actions_$name' name='actions_$name' class='nav_action' style='display:none;'>" . str_replace(".php", "", makefileopts($files[0])) . "</select>";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td><?php echo _t("Language") ?></td>
            <td>
                <select name="language"><?php echo getLanguages() ?></select>
            </td>
        </tr>
        <tr>
            <td><input type="submit" name="create" value="<?php echo _t("Create item") ?>" /></td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    $(document).ready(function(){
        var application = $("#application").val();
        $("#actions_"+application).show();
        $("#application").change(function(){
            var application = $(this).val();
            $(".nav_action").hide();
            $("#actions_"+application).show();
        });
    });
</script>