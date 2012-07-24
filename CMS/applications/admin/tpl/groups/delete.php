<?php
try {

    if (!isset($_GET['id']))
        throw new Exception("Missing group id");

    if (isset($_GET['confirm'])) {
        ?>
        <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
            <center>Are you sure you wish to delete this?<br /><br />
                <a href="<?php echo page("admin", "groups", "delete", "", "", "id=" . $_GET['id']) ?>"><span class="btn red"><?php echo _t("Yes") ?></span></a> 
                <a href="<?php echo page("admin", "groups") ?>"><span class="btn"><?php echo _t("No") ?></span></center></a>
        </div>
        <?
    } else {
        $db = new DB("groups");
        $db->delete("group_id = '" . $db->escape($_GET['id']) . "'");
        header("location: " . page("admin", "groups"));
    }
} catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
