<h4><?php echo _t("Addons") ?></h4>
<?php
$addons = makefilelist(PATH_APPLICATIONS, ".|..|index.html", true, "folders");
?>

<table class="forum" width="100%" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <td class="border-bottom" width="40%"><?php echo _t("Addon") ?></td>
            <td class="border-bottom border-right" width="100px"></td>
            <td class="border-bottom border-right"><?php echo _t("Group") ?></td>
            <td class="border-bottom"><?php echo _t("Installed") ?></td>
        </tr>
    </thead>
    <?
    foreach ($addons as $addon) {
        $app = new Addon($addon);
        if ($app->checkInstall()) {

            $db = new DB("addons");
            $db->join("left", "{PREFIX}groups", "addon_group", "group_id");
            $db->select("addon_name = '" . $db->escape($addon) . "'");
            if ($db->numRows()) {
                $db->nextRecord();
                $group = $db->group_name;
                $installed = get_date($db->addon_added);
            } else {
                $group = "";
                $installed = "";
            }
            ?>
            <tr>
                <td class="border-bottom"><?php echo $addon ?></td>
                <td class="border-bottom border-right" align="right">
                    <?php
                    if ($app->isInstalled()) {
                        ?>
                        <?php
                        if ($app->hasAdmin()) {
                            ?>
                            <a href="<?php echo page("admin", "addons", "admin", $addon) ?>" class="rel" title="Admin"><img src="images/icons/admin_16.png" /></a>
                            <?php
                        }
                        ?>
                        <a href="<?php echo page("admin", "addons", "edit", $addon) ?>" class="rel" title="Edit"><img src="images/icons/edit_16.png" /></a>
                        <a href="<?php echo page("admin", "addons", "uninstall", "", "", "addon=" . $addon . "&confirm") ?>" class="rel" title="<?php echo _t("Uninstall") ?>"><img src="images/icons/trash_16.png" /></a>
                        <?php
                    } else {
                        echo "<a href='" . page("admin", "addons", "install", $addon) . "'><span class='btn'>" . _t("Install") . "</span></a>";
                    }
                    ?>
                </td>
                <td class="border-bottom border-right"><?php echo $group ?></td>
                <td class="border-bottom"><?php echo $installed ?></td>
            </tr>
            <?
        }
    }
    ?>

</table>