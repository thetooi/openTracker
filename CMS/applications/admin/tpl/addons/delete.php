<?php
try {

    if (!isset($_GET['addon']))
        throw new Exception("Missing data");

    if (isset($_GET['confirm'])) {
        ?>
        <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
            <center><?php echo _t("Are you sure you wish to uninstall this?") ?><br /><br />
                <a href="<?php echo page("admin", "addons", "uninstall", "", "", "addon=" . $_GET['addon']) ?>">
                    <span class="btn red"><?php echo _t("Yes") ?></span>
                </a> 
                <a href="<?php echo page("admin", "addons") ?>">
                    <span class="btn"><?php echo _t("No") ?></span>
                </a>
            </center>
        </div>
        <?
    } else {
        $db = new DB("addons");
        $db->delete("addon_name = '" . $db->escape($_GET['addon']) . "'");
        header("location: " . page("admin", "addons"));
    }
} catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
