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

$flags = makefilelist(PATH_IMAGES . "flags", ".|..|index.html", true);
?>
<h4>Create new language file</h4>
<script type="text/javascript">
    $(document).ready(function(){
        $("#flag").change(function(){
            var val = $(this).val();
            $("#flag_image").attr("src", "images/flags/"+val);
        });
        
        var val = $("#flag").val();
        $("#flag_image").attr("src", "images/flags/"+val);
    });
</script>
<form method="post">
    <table>
        <tr><td><?php echo _t("Name") ?></td><td><input type="text" name="name" value=""></td></tr>
        <tr><td><?php echo _t("Id") ?></td><td><input type="text" name="id" value=""></td></tr>
        <tr><td><?php echo _t("Flag") ?></td><td><img src = "" id = "flag_image"><select id = "flag" name = "flag"><?php echo makefileopts($flags) ?></select></td></tr>
        <tr><td><input type="submit" name="save" value="<?php echo _t("Save") ?>"></td></tr>
    </table>
</form>

<?php
if (isset($_POST['save'])) {
    try {

        if (empty($_POST['name']) || empty($_POST['id']) || empty($_POST['flag']))
            throw new Exception("missing data");

        $db = new DB("system_languages");
        $db->select("language_id = '" . $db->escape($_POST['id']) . "'");

        if ($db->numRows())
            throw new Exception("A language with that id is already installed");

        $db = new DB("system_languages");
        $db->language_id = $_POST['id'];
        $db->language_name = $_POST['name'];
        $db->language_flag = $_POST['flag'];
        $db->language_installed = time();
        $db->insert();
        header("location: " . page("admin", "translations", "edit", $_POST['id']));
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>