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

    $this->setSidebar(true);

    if (!isset($_GET['ticket']))
        throw new Exception("invalid ticket id");

    $db = new DB("support");
    $db->setColPrefix("ticket_");
    $db->select("ticket_id = '" . $db->escape($_GET['ticket']) . "'");
    if (!$db->numRows())
        throw new Exception("ticket not found");

    $ticket_id = $_GET['ticket'];

    $db->nextRecord();

    if (isset($_POST['reply'])) {
        $db = new DB("support");
        $db->ticket_status = $_POST['status'];
        $db->update("ticket_id = '" . $db->escape($ticket_id) . "'");
    }


    $db = new DB("support");
    $db->setColPrefix("ticket_");
    $db->select("ticket_id = '" . $db->escape($ticket_id) . "'");
    if (!$db->numRows())
        throw new Exception("ticket not found");

    $ticket_id = $_GET['ticket'];

    $db->nextRecord();
    switch ($db->status) {
        default:
            $msg = "<font color='red'>" . _t("unsolved") . "</font>";
            break;
        case 1:
            $msg = "<font color='green'>" . _t("solved") . "</font>";
            break;
    }
    ?>
    <h4><?php echo $db->subject; ?>: <?php echo $msg; ?></h4>
    <form method="post">
        <select name="status">
            <option value='0' <?php echo ($db->status == 0 ? "SELECTED" : "") ?>><?php echo _t("Unsolved") ?></option>
            <option value='1' <?php echo ($db->status == 1 ? "SELECTED" : "") ?>><?php echo _t("Solved") ?></option>
        </select><br />
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <?php echo bbeditor("message", 7, 70) ?><br />
        <input type="submit" name="reply" value="<?php echo _t("Reply") ?>">
    </form>
    <div id="conv">
        <?php
        if (isset($_POST['reply'])) {
            try {

                if ($_POST['secure_input'] != $_SESSION['secure_token'])
                    throw new Exception("Wrong secured token");

                if (!empty($_POST['message'])) {
                    $db = new DB("support_messages");
                    $db->setColPrefix("message_");
                    $db->user = USER_ID;
                    $db->added = time();
                    $db->content = $_POST['message'];
                    $db->ticket = $ticket_id;
                    $db->insert();
                }
            } Catch (Exception $e) {
                echo error(_t($e->getMessage()));
            }
        }

        $db = new DB("support_messages");
        $db->setColPrefix("message_");
        $db->setSort("message_added DESC");
        $db->select("message_ticket = '" . $db->escape($ticket_id) . "'");
        while ($db->nextRecord()) {
            $user = new Acl($db->user);
            ?>
            <div class="item">
                <div class="avatar">
                    <?php echo "<img src='" . $user->avatar() . "' style='max-width:70px'>"; ?>
                </div>
                <div class="sender">
                    <a href="<?php echo page("profile", "view", $user->name) ?>"><b><?php echo $user->name ?></b></a><br />
                    <?php echo htmlformat($db->content, true); ?>
                </div>
                <div class="date">
                    <?php echo get_date($db->added); ?>
                </div>
            </div>
            <?
        }
        ?>
    </div>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
