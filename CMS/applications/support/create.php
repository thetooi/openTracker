<?php
$this->setSidebar(true);
?>

<h4><?php echo _t("Open new support ticket"); ?></h4>
<form method="post">
    <table>
        <tr>
            <td>Subject:</td>
            <td><input type="text" name="subject" size="40" /></td>
        </tr>
        <tr>
            <td colspan="2"><?php echo bbeditor("message", 12, 70) ?></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="open" value="<?php echo _t("Open ticket") ?>"></td>
        </tr>
    </table>
</form>

<?php
if (isset($_POST['open'])) {
    try {
        if (empty($_POST['subject']))
            throw new Exception("missing form");

        if (empty($_POST['message']))
            throw new Exception("missing form");
        $ticket_id = uniqid(true);
        $db = new DB("support");
        $db->setColPrefix("ticket_");
        $db->id = $ticket_id;
        $db->user = USER_ID;
        $db->added = time();
        $db->subject = $_POST['subject'];
        $db->status = 0;
        $db->insert();

        $db = new DB("support_messages");
        $db->setColPrefix("message_");
        $db->user = USER_ID;
        $db->added = time();
        $db->content = $_POST['message'];
        $db->ticket = $ticket_id;
        $db->insert();

        header("location: " . page("support", "view", "", "", "", "ticket=" . $ticket_id));
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>