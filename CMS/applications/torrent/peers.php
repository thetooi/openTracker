<?php

try {
    $acl = new Acl(USER_ID);
    $tid = isset($_GET['torrent']) ? $_GET['torrent'] : "";

    if (!intval($_GET['torrent']))
        throw new Exception("invalid id");

    $db = new DB("peers");
    $db->setColPrefix("peer_");
    $db->select("peer_torrent = '" . $db->escape($tid) . "' AND peer_seeder = '1'");

    $no_peers = true;

    if ($db->numRows()) {
        echo "<h4>" . _t("Seeders") . "</h4>";
        echo "
        <table width='100%' class='forum' cellspacing='0' cellpadding='5'>
        <thead>
            <tr>
                <td class='border-bottom border-right' width='200px'>Seeder</td>
                <td class='border-bottom border-right'>Downloaded</td>
                <td class='border-bottom border-right'>Uploaded</td>
                <td class='border-bottom border-right' align='center'>Seeded for</td>
            </tr>
        </thead>
        <tbody>
        ";
        while ($db->nextRecord()) {
            $user = new Acl($db->userid);
            echo "
            <tr>
                <td class='border-bottom border-right'>" . (!$user->anonymous || $acl->Access("x") ? "<a href='" . page("profile", "view", $user->name) . "'>" . $user->name . "</a>" : "Anonymous") . "</td>
                <td class='border-bottom border-right'>" . bytes($db->downloaded) . "</td>
                <td class='border-bottom border-right'>" . bytes($db->uploaded) . "</td>
                <td class='border-bottom border-right' align='center'>" . timediff($db->started, time()) . "</td>
            </tr>";
        }
        echo "</tbody></table>";
        $no_peers = false;
    }

    $db->select("peer_torrent = '" . $db->escape($tid) . "' AND peer_seeder = '0'");
    if ($db->numRows()) {
        echo "<h4>" . _t("Leechers") . "</h4>";
        echo "
        <table width='100%' class='forum' cellspacing='0' cellpadding='5'>
        <thead>
            <tr>
                <td class='border-bottom border-right' width='200px'>Leecher</td>
                <td class='border-bottom border-right'>Downloaded</td>
                <td class='border-bottom border-right'>Uploaded</td>
                <td class='border-bottom border-right' align='center'>Leeching for</td>
            </tr>
        </thead>
        <tbody>
        ";
        while ($db->nextRecord()) {
            $user = new Acl($db->userid);
            echo "
            <tr>
                <td class='border-bottom border-right'>" . (!$user->anonymous || $acl->Access("x") ? "<a href='" . page("profile", "view", $user->name) . "'>" . $user->name . "</a>" : "Anonymous") . "</td>
                <td class='border-bottom border-right'>" . bytes($db->downloaded) . "</td>
                <td class='border-bottom border-right'>" . bytes($db->uploaded) . "</td>
                <td class='border-bottom border-right' align='center'>" . timediff($db->started, time()) . "</td>
            </tr>";
        }
        echo "</tbody></table>";
        $no_peers = false;
    }


    if ($no_peers) {
        echo notice(_t("No Peers found"));
    }
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
