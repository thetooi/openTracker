<?php
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
        $time->offset = $_POST['offset'];
        $time->long = $_POST['time_long'];
        $time->update();
        $spref->registration = isset($_POST['registration']) ? 1 : 0;
        $spref->max_users = $_POST['max_users'];
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

<h4><?php echo _t("Website Settings"); ?></h4>
<form method="POST">
    <fieldset>
        <legend>System settings</legend>
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <table>
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
        </table>
    </fieldset>
    <fieldset>
        <legend>Time settings</legend>
        <table>
            <tr>
                <td width="120px"><?php echo _t("Timezone"); ?>:</td>
                <td><select name="offset"><?php echo timezones($time->offset); ?></select></td>
            </tr>
            <tr>
                <td><?php echo _t("Time format"); ?>:</td>
                <td><input name="time_long" type="text" value="<?php echo $time->long ?>" size="14" /> <?php echo get_date(time(), "", 1, 0) ?></td>
            </tr>        
        </table>
    </fieldset>

    <fieldset>
        <legend>Member settings</legend>
        <table>
            <tr>
                <td width="120px"><?php echo _t("Open registration"); ?></td>
                <td><input type="checkbox" name="registration" <?php echo $spref->registration == 1 ? "CHECKED" : "" ?>></td>
            </tr>     
            <tr>
                <td width="120px"><?php echo _t("Max users"); ?></td>
                <td><input type="text" name="max_users" size="10" value="<?php echo $spref->max_users ?>" /></td>
            </tr>   
        </table>
    </fieldset>
    <table>
        <tr>
            <td><input type="submit" name="save" value="<?php echo _t("Save settings"); ?>" /></td>
        </tr>
    </table>
</form>