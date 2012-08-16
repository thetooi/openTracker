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
if (!defined("INCLUDED"))
    die("Access denied");

try {

    $acl = new Acl(USER_ID);

    if (!$acl->Access("x"))
        throw new Exception("Access denied");

    if (!isset($this->args['var_a']) && empty($this->args['var_a']))
        throw new Exception("Missing variable");

    $id = end(explode("-", $this->args['var_a']));

    if (isset($_POST['save'])) {
        $db = new DB("forum_topics");
        $db->setColPrefix("topic_");
        $db->subject = $_POST['subject'];
        $db->locked = $_POST['locked'];
        $db->sticky = $_POST['sticky'];
        $db->update("topic_id = '" . $db->escape($_POST['id']) . "'");
        header("location: " . page("forums", "view-topic", $_POST['subject'] . "-" . $_POST['id']));
    }

    if (isset($_POST['delete'])) {
        header("location: " . page("forums", "delete-topic", "", "", "", "id=" . $_POST['id']."&confirm"));
    }

    $db = new DB("forum_topics");
    $db->setColPrefix("topic_");
    $db->select("topic_id = '" . $id . "'");

    if (!$db->numRows())
        throw new Exception("Topic not found");

    $db->nextRecord();
    ?>
    <h4>Editing Topic</h4>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $db->id ?>">
        <table>
            <tr>
                <td>Subject</td>
                <td><input type="text" name="subject" size="30" value="<?php echo $db->subject ?>"></td>
            </tr>
            <tr>
                <td>Sticky</td>
                <td>
                    <label><input name="sticky" value="1" type="radio" <?php echo ($db->sticky == 1 ? "CHECKED" : "") ?>> Yes</label>
                    <label><input name="sticky" value="0" type="radio" <?php echo ($db->sticky == 0 ? "CHECKED" : "") ?>> No</label>
                </td>
            </tr>
            <tr>
                <td>Locked</td>
                <td>
                    <label><input name="locked" value="1" type="radio" <?php echo ($db->locked == 1 ? "CHECKED" : "") ?>> Yes</label>
                    <label><input name="locked" value="0" type="radio" <?php echo ($db->locked == 0 ? "CHECKED" : "") ?>> No</label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name="save" class="blue" value="<?php echo _t("Save Topic") ?>">
                    <input type="submit" name="delete" class="red" value="<?php echo _t("Delete Topic") ?>">
                </td>
            </tr>
        </table>
    </form>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
