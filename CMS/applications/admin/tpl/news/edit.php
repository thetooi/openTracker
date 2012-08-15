<?php

/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Wuild
 * @package openTracker
 */

if(!defined("INCLUDED"))
    die("Access denied");

try {


    if (!isset($_GET['id']))
        throw new Exception("Missing news id");

    if (!intval($_GET['id']))
        throw new Exception("Invalid id");

    $id = 0 + $_GET['id'];

    if (isset($_POST['save'])) {

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
        $db->update("news_id = '" . $id . "'");
        echo notice(_t("News post has been saved"));
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

    $db = new DB("news");
    $db->setColPrefix("news_");
    $db->select("news_id = '" . $db->escape($id) . "'");
    $db->nextRecord();
    ?>

    <h4><?php echo _t("Editing"); ?>: <?php echo htmlformat($db->subject) ?></h4><br />
    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <?php echo _t("Subject"); ?>: <input type="text" name="subject" size="50" value="<?php echo $db->subject ?>"><br />
        <?php echo bbeditor("content", 17, 70, $db->content) ?>
        <input type="submit" name="save" value="<?php echo _t("Save"); ?>" /> <input type="submit" name="preview" value="<?php echo _t("Preview"); ?>" />
    </form>

    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>