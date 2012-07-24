<?php
try {

    if (!isset($_GET['id']))
        throw new Exception("Missing news id");

    if (isset($_GET['confirm'])) {
        ?>
        <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
            <center><?php echo _t("Are you sure you wish to delete this?") ?><br /><br />
                <a href="<?php echo page("admin", "news", "delete", "", "", "id=" . $_GET['id']) ?>"><span class="btn red"><?php echo _t("Yes"); ?></span></a> 
                <a href="<?php echo page("admin", "news") ?>"><span class="btn"><?php echo _t("No"); ?></span></center></a>
        </div>
        <?
    } else {
        $db = new DB("news");
        $db->delete("news_id = '" . $db->escape($_GET['id']) . "'");
        header("location: " . page("admin", "news"));
    }
} catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
