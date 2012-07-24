<script>
    $(document).ready(function(){
        $(".item").live("click", function(){
            var uid = $(this).attr("rel");  
            if(uid != undefined)
                window.location = "<?php echo page("profile", "mailbox", "view", "", "", "uid=") ?>"+uid;
        });
    });
</script>
<div id="conv">
    <?php
    $this->setSidebar(true);
    $this->setTitle("Mailbox");

    function getLastMsg($uid) {
        $db = new DB("messages");
        $db->setColPrefix("message_");
        $db->setSort("message_added DESC");
        $db->select("message_sender = '" . USER_ID . "' AND message_receiver = '" . $uid . "' OR message_sender = '" . $uid . "' AND message_receiver = '" . USER_ID . "'");
        $db->nextRecord();



        return array(
            "added" => $db->added
        );
    }

    $actions = (isset($this->args['var_a'])) ? $this->args['var_a'] : "";
    switch ($actions) {
        default:
            ?>
            <h4><?php echo _t("Conversations"); ?></h4>
            <?php
            $db = new DB("messages");
            $db->query("SELECT * FROM (select * from `{PREFIX}messages` order by message_added desc) as my_table_tmp WHERE message_receiver = '" . USER_ID . "' GROUP BY message_sender ORDER BY message_added DESC");
            $db->setColPrefix("message_");
            if ($db->numRows()) {
                echo _t("Click on a conversation to view the whole conversation") . "<br /><br />";
                while ($db->nextRecord()) {
                    $tpl = new Template(PATH_APPLICATIONS . "profile/tpl/");
                    $tpl->item = $db->id;
                    $tpl->loadFile("convo_contact_item.php");
                    $tpl->build();
                }
            } else {
                echo _t("No conversations found");
            }
            ?>
            <?php
            break;

        case 'view':
            try {
                $uid = $_GET['uid'];

                $acl = new Acl($uid);

                echo "<h4>" . _t("Conversation with") . " " . $acl->name . "</h4>";

                $tpl = new Template(PATH_APPLICATIONS . "profile/tpl/");
                $tpl->uid = $uid;
                $tpl->loadFile("convo_reply.php");
                $tpl->build();

                $db = new DB("messages");
                $db->setColPrefix("message_");
                $db->setSort("message_added DESC");
                $db->select("message_sender = '" . USER_ID . "' AND message_receiver = '" . $uid . "' OR message_sender = '" . $uid . "' AND message_receiver = '" . USER_ID . "'");

                if ($db->numRows()) {

                    while ($db->nextRecord()) {
                        $tpl = new Template(PATH_APPLICATIONS . "profile/tpl/");
                        $tpl->item = $db->id;
                        $tpl->loadFile("convo_item.php");
                        $tpl->build();
                    }

                    $db = new DB("messages");
                    $db->message_unread = "no";
                    $db->update("message_sender = '" . $uid . "' AND message_receiver = '" . USER_ID . "'");
                } else {
                    echo _t("No messages in this conversation yet.");
                }
            } Catch (Exception $e) {
                echo error(_t($e->getMessage()));
            }
            break;
    }
    ?>
</div>