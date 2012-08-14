<?php
try {

    if (!isset($_GET['ticket']))
        throw new Exception("Missing id");

    if (isset($_GET['confirm'])) {
        ?>
        <div class="confirm">
            <center><?php echo _t("Are you sure you wish to delete this?") ?><br /><br />
                <a href="<?php echo page("admin", "support", "delete", "", "", "ticket=" . $_GET['ticket']) ?>"><span class="btn red"><?php echo _t("Yes"); ?></span></a> 
                <a href="<?php echo page("admin", "support") ?>"><span class="btn"><?php echo _t("No"); ?></span></center></a>
        </div>
        <?
    } else {
        $db = new DB("support");
        $db->delete("ticket_id = '" . $db->escape($_GET['ticket']) . "'");
        $db = new DB("support_messages");
        $db->delete("message_ticket = '" . $db->escape($_GET['ticket']) . "'");
        header("location: " . page("admin", "support"));
    }
} catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
