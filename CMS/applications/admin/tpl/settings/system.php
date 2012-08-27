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
if (!defined("INCLUDED"))
    die("Access denied");

$wpref = new Pref("website");
$spref = new Pref("system");
$time = new Pref("time");

if (isset($_POST['save'])) {

    try {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secure token");

        $wpref->name = $_POST['name'];
        $wpref->cleanurls = isset($_POST['clean_url']) ? 1 : 0;
        $wpref->noreply_email = $_POST['email'];
        $wpref->language = $_POST['language'];
        $wpref->startapp = $_POST['startapp'];
        $wpref->update();
        $spref->template = $_POST['template'];
        $spref->update();

        echo notice(_t("System settings saved."));
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}

$templates = makefilelist(PATH_TEMPLATES, ".|..|index.html", true, "folders");

$apps = makefilelist(PATH_APPLICATIONS, ".|..", true, "folders");
?>
<form method="POST">
    <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
    <table cellspacing="0" width="600px">
        <tr>
            <td width="120px"><?php echo _t("Website name"); ?>:</td>
            <td><input type="text" name="name" value="<?php echo $wpref->name; ?>" size="40" /></td>
        </tr>
        <tr>
            <td width="120px"><?php echo _t("Template"); ?>:</td>
            <td><select name="template"><?php echo makefileopts($templates, $spref->template); ?></select></td>
        </tr>
        <tr>
            <td width="120px"><?php echo _t("Startpage"); ?>:</td>
            <td><select name="startapp"><?php echo makefileopts($apps, $wpref->startapp); ?></select></td>
        </tr>
        <tr>
            <td width="120px"><?php echo _t("Default language"); ?>:</td>
            <td><select name="language"><?php echo getLanguages($wpref->language); ?></select></td>
        </tr>
        <tr>
            <td><?php echo _t("No-Reply Email"); ?>:</td>
            <td><input name="email" type="text" value="<?php echo $wpref->noreply_email ?>" size="34" /></td>
        </tr>
        <tr>
            <td><label for="clean_url"><?php echo _t("Clean Urls"); ?></label></td>
            <td><input type="checkbox" name="clean_url" id="clean_url" <?php echo ($wpref->cleanurls == 1) ? "CHECKED" : "" ?> /> <?php echo _t("mod_rewrite is required"); ?></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="save" value="<?php echo _t("Save settings"); ?>" /></td>
        </tr>
    </table>
</form>