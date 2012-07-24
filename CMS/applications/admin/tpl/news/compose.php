<?php
try {

    if (isset($_POST['compose'])) {
        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        if (empty($_POST['subject']))
            throw new Exception("News post has to have a subject");

        if (empty($_POST['content']))
            throw new Exception("News post cannot be empty");

        $db = new DB("news");
        $db->setColPrefix("news_");
        $db->content = $_POST['content'];
        $db->subject = $_POST['subject'];
        $db->added = time();
        $db->userid = USER_ID;
        $db->insert();
        header("location: " . page("admin", "news"));
    }


    if (isset($_POST['preview'])) {
        ?>
        <div class="news">
            <h4><?php echo htmlformat($_POST['subject']); ?></h4>
            <small><?php echo get_date(time(), "", 1) ?></small>
            <p><?php echo htmlformat($_POST['content'], true); ?></p>
        </div>
        <?php
    }
    ?>

    <h4><?php echo _t("Composing news post"); ?></h4><br />
    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <?php echo _t("Subject"); ?>: <input type="text" name="subject" size="50" value=""><br />
        <?php echo bbeditor("content", 17, 70) ?>
        <input type="submit" name="compose" value="<?php echo _t("Publish"); ?>" /> <input type="submit" name="preview" value="<?php echo _t("Preview"); ?>" />
    </form>

    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>