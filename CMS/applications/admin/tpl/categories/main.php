<a href="<?php echo page("admin", "categories", "create"); ?>"><span class="btn"><?php echo _t("Create category") ?></span></a>
<br /><br />
<table width="100%" class="forum" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <td class="border-bottom" width="44px"></td>
            <td class="border-bottom"><?php echo _t("Name") ?></td>
            <td class="border-bottom border-right" width="36px"></td>
            <td class="border-bottom border-right" width="150px">Icon</td>
            <td class="border-bottom" width="100px">Torrents</td>
        </tr>
    </thead>
    <tbody>
        <?php
        $db = new DB("categories");
        $db->setColPrefix("category_");
        $db->setSort("category_name ASC");
        $db->select();
        while ($db->nextRecord()) {

            $db2 = new DB;
            $db2->query("SELECT COUNT(torrent_id) as torrents FROM {PREFIX}torrents WHERE torrent_category = '" . $db->id . "'");
            $db2->nextRecord();
            ?>
            <tr>
                <td class="border-bottom"><img src="images/categories/<?php echo $db->icon ?>" /></td>
                <td class="border-bottom"><?php echo $db->name ?></td>
                <td class="border-bottom border-right">
                    <a href="<?php echo page("admin", "categories", "edit", "", "", "id=" . $db->id); ?>" class="rel" title="<?php echo _t("Edit") ?>"><img src="images/icons/edit_16.png"></a> 
                    <a href="<?php echo page("admin", "categories", "delete", "", "", "id=" . $db->id."&confirm"); ?>" class="rel" title="<?php echo _t("Delete") ?>"><img src="images/icons/trash_16.png"></a> 
                </td>
                <td class="border-bottom border-right"><?php echo $db->icon ?></td>
                <td class="border-bottom"><?php echo $db2->torrents; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>