<?php

class notifications_main {

    function __construct() {
        //echo "hello";
    }

    function add($user_id, $type, $data) {
        $db = new DB("notifications");
        $db->setColPrefix("notification_");
        $db->type = $type;
        $db->added = time();
        $db->user = $user_id;
        $db->data = $data;
        //$db->unread = 1;
        $db->insert();
    }

    function getNew() {
        $db = new DB;
        $db->query("SELECT COUNT(notification_id) as new_notifi FROM {PREFIX}notifications WHERE notification_unread = '1' AND notification_user = '" . USER_ID . "'");
        $db->nextRecord();
        return $db->new_notifi;
    }

    function load($limit = 5) {
        $db = new DB("notifications");
        $db->setColPrefix("notification_");
        $db->setSort("notification_added DESC");
        if (intval($limit))
            $db->setLimit($limit);
        $db->select("notification_user = '" . USER_ID . "'");
        if ($db->numRows()) {
            $return = "";
            while ($db->nextRecord()) {
                $return .= "<li>";
                switch ($db->type) {
                    case 'friend':
                        $data = json_decode($db->data);
                        $user = new Acl($data->user);
                        switch ($data->type) {
                            case 'accept':
                                $return .= "<b><a href='" . page("profile", "view", $user->name) . "'>" . $user->name . "</a></b> <small>" . get_date($db->added) . "</small> <br /> " . _t("Has accepted your friend request");
                                break;

                            case 'decline':
                                $return .= "<b><a href='" . page("profile", "view", $user->name) . "'>" . $user->name . "</a></b> <small>" . get_date($db->added) . "</small> <br /> " . _t("Has declined your friend request");
                                break;

                            case 'remove':
                                $return .= "<b><a href='" . page("profile", "view", $user->name) . "'>" . $user->name . "</a></b> <small>" . get_date($db->added) . "</small> <br /> " . _t("Has removed you from his friends list");
                                break;
                        }
                        break;

                    case 'system':
                        $data = json_decode($db->data);

                        $group = new DB("groups");
                        $group->setColPrefix("group_");
                        $group->select("group_id = '" . $data->group . "'");
                        $group->nextRecord();

                        switch ($data->type) {
                            case 'upgrade':
                                $return .=_t("You have been upgraded to ") . "<b>" . $group->name . "</b><br /><small>" . get_date($db->added) . "</small>";
                                break;

                            case 'downgrade':
                                $return .=_t("You have been demoted to ") . "<b>" . $group->name . "</b><br /><small>" . get_date($db->added) . "</small>";
                                break;
                        }
                        break;
                }
                $return .="</li>";
            }
        } else {
            $return = "<li>" . _t("No notifications found") . "</li>";
        }

        echo $return;
    }

}

?>
