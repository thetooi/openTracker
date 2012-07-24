<?php
try {

    if (!$this->addon)
        throw new Exception("missing data");

    $addon = new Addon($this->addon);
    if (!$addon->checkInstall())
        throw new Exception("addons is not installable");

    if ($addon->isInstalled())
        throw new Exception("addon is already installed");

    if (isset($_POST['install'])) {
        try {
            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("Wrong secured token");

            $addon->Install();
            $db = new DB("addons");
            $db->setColPrefix("addon_");
            $db->name = $this->addon;
            $db->installed = true;
            $db->added = time();
            $db->group = $_POST['group'];
            $db->insert();

            header("location: ".page("admin", "addons"), true);
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }
    ?>
    <h4><?php echo _t("Install Addon") ?></h4>
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
                    <select name="group"><?php echo getGroups() ?></select> <?php echo _t("and above") ?>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="install" value="<?php echo _t("Install") ?>">
                </td>
            </tr>
        </table>
    </form>
    <?
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
