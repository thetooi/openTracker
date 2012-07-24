<?php
include(PATH_LIBRARY . "Main.php");
include(PATH_LIBRARY . "Pref.php");
include(PATH_LIBRARY . "DB.php");

$wpref = new Pref("website");
$time = new Pref("time");

try {

    if ($_SESSION['action'] != "step5")
        throw new Exception("Access denied");
    if (isset($_POST['save'])) {

        try {


            $wpref->url = $_POST['url'];
            $wpref->name = $_POST['name'];
            $wpref->cleanurls = isset($_POST['clean_url']) ? 1 : 0;
            $wpref->noreply_email = $_POST['email'];
            $wpref->update();
            $time->offset = $_POST['offset'];
            $time->update();

            $_SESSION['action'] = "step6";
            header("location: ?action=finish");
        } Catch (Exception $e) {
            echo error($e->getMessage());
        }
    }

    $templates = makefilelist(PATH_TEMPLATES, ".|..|index.html", true, "folders");

    $apps = makefilelist(PATH_APPLICATIONS, ".|..", true, "folders");
    ?>

    <h4>Website Settings</h4>
    <form method="POST">
        <table>
            <tr>
                <td width="120px">Website url:</td>
                <td><input type="text" name="url" value="<?php echo (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER["SERVER_NAME"] . "/"; ?>" size="40" /></td>
            </tr>
            <tr>
                <td width="120px">Website name:</td>
                <td><input type="text" name="name" value="<?php echo $wpref->name; ?>" size="40" /></td>
            </tr>
            <tr>
                <td>No-Reply Email:</td>
                <td><input name="email" type="text" value="<?php echo $wpref->noreply_email ?>" size="34" /></td>
            </tr>
            <tr>
                <td><label for="clean_url">Clean Urls</label></td>
                <td><input type="checkbox" name="clean_url" id="clean_url" <?php echo ($wpref->cleanurls == 1) ? "CHECKED" : "" ?> /> mod_rewrite is required</td>
            </tr>
        </table>
        <table>
            <tr>
                <td width="120px">Timezone:</td>
                <td><select name="offset"><?php echo timezones($time->offset); ?></select></td>
            </tr>     
        </table>
        <table>
            <tr>
                <td><input type="submit" name="save" value="Finnish" /></td>
            </tr>
        </table>
    </form>

    <?php
} Catch (Exception $e) {
    echo error($e->getMessage());
}
?>