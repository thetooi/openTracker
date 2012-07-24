<?php
try {

    if (!$this->addon)
        throw new Exception("missing data");

    $addon = new Addon($this->addon);
    if (!$addon->checkInstall())
        throw new Exception("addons is not installable");

    if (isset($_POST['save'])) {
        try {
            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("Wrong secured token");

            $addon->Install();
            $db = new DB("addons");
            $db->setColPrefix("addon_");
            $db->installed = true;
            $db->group = $_POST['group'];
            $db->update("addon_name = '" . $db->escape($this->addon) . "'");

            header("location: " . page("admin", "addons"), true);
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }

    $db = new DB("addons");
    $db->select("addon_name = '" . $db->escape($this->addon) . "'");
    $db->nextRecord();
    ?>
    <h4><?php echo _t("Edit Addon") ?></h4>
    <form method="POST">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <table>
            <tr>
                <td width="120px">
                    <?php echo _t("Addon") ?>
                </td>
                <td>
                    <?php echo $this->addon ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo _t("Visible for") ?>
                </td>
                <td>
                    <select name="group"><?php echo getGroups($db->addon_group) ?></select> <?php echo _t("and above") ?>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="save" value="<?php echo _t("Save") ?>">
                </td>
            </tr>
        </table>
    </form>
    <?
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
