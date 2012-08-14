<?php
try {

    if (!isset($_GET['id']))
        throw new Exception("Missing id");

    if (isset($_GET['confirm'])) {
        ?>
        <div class="confirm">
            <center><?php echo _t("Are you sure you wish to delete this?") ?><br /><br />
                <a href="<?php echo page("admin", "categories", "delete", "", "", "id=" . $_GET['id']) ?>"><span class="btn red"><?php echo _t("Yes") ?></span></a> 
                <a href="<?php echo page("admin", "categories") ?>"><span class="btn"><?php echo _t("No") ?></span></center></a>
        </div>
        <?
    } else {
        $db = new DB("categories");
        $db->delete("category_id = '" . $db->escape($_GET['id']) . "'");
        header("location: " . page("admin", "categories"));
    }
} catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
