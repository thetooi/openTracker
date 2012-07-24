<?php
if (isset($_POST['reply'])) {
    try {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        if (!intval($this->uid))
            throw new Exception("Missing unique user id");

        if (empty($_POST['msg']))
            throw new Exception("Cannot send an empty message");

        $db = new DB("messages");
        $db->setColPrefix("message_");
        $db->sender = USER_ID;
        $db->receiver = $this->uid;
        $db->content = $_POST['msg'];
        $db->added = time();
        $db->insert();
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}

if ($this->uid != 0) {
    ?>

    <div class="reply">
        <form method="post">
            <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
            <div style="float:left; width: 460px; padding-left: 120px;">
                <?php echo bbeditor("msg", 5, 52); ?>
            </div>
            <input type="submit" name="reply">
        </form>
    </div>

    <?php
}
?>