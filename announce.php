<?php

/**
 * filename announce.php
 * 
 * @author Wuild
 * @package openTracker
 */
include("init.php");
error_reporting(1);

$data = array(
    "ip" => $_SERVER['REMOTE_ADDR']
);

$callback = array();

$user_cols = array("id", "uploaded", "downloaded", "group", "status");
$torrent_cols = array("id", "seeders", "leechers", "added", "times_completed");
$peer_cols = array("seeder", "peer_id", "ip", "port", "uploaded", "downloaded", "userid");

try {

    $agent = $_SERVER["HTTP_USER_AGENT"];
    if (preg_match('%^Mozilla/|^Opera/|^Links |^Lynx/%i', $agent) || isset($_SERVER['HTTP_COOKIE']) || isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || isset($_SERVER['HTTP_ACCEPT_CHARSET']))
        throw new Exception("Access Denied");

    $parts = array();
    if (!isset($_GET['passkey']) OR !preg_match("/[0-9a-fA-F]{32}/", $_GET['passkey'], $parts))
        throw new Exception("Invalid Passkey");
    else
        $data['passkey'] = $parts[0];

    foreach (array("info_hash", "peer_id", "event", "ip", "localip") as $x) {
        if (isset($_GET["$x"]))
            $data[$x] = "" . $_GET[$x];
    }

    foreach (array("port", "downloaded", "uploaded", "left") as $x) {
        if (isset($_GET["$x"]))
            $data[$x] = 0 + $_GET[$x];
    }

    foreach (array("passkey", "info_hash", "peer_id", "port", "downloaded", "uploaded", "left") as $x) {
        if (!isset($data[$x]))
            throw new Exception("Error 1: Missing key: $x");
    }


    $readsize = 50;
    foreach (array("num want", "numwant", "num_want") as $k) {
        if (isset($_GET[$k])) {
            $readsize = 0 + $_GET[$k];
            break;
        }
    }

    if (!isset($data['event']))
        $data['event'] = "";

    $data['info_hash'] = bin2hex($data['info_hash']);

    $data['seeder'] = ($data['left'] == 0) ? true : false;

    $user = new DB("users");
    $user->setCols($user_cols);
    $user->setColPrefix("user_");
    $user->select("user_passkey = '" . $user->escape($data['passkey']) . "'");

    if (!$user->numRows())
        throw new Exception("Error 2: Unknown passkey. Please redownload the torrent");

    $user->nextRecord();

    if ($user->status == 0 || $user->status == 1 || $user->status == 2 || $user->status == 3)
        throw new Exception("Error 3: Permission denied");

    $torrent = new DB("torrents");
    $torrent->setCols($torrent_cols);
    $torrent->setColPrefix("torrent_");
    $torrent->select("torrent_info_hash = '" . $data['info_hash'] . "'");
    if (!$torrent->numRows())
        throw new Exception("Error 4: Torrent not found!");

    $torrent->nextRecord();

    $torrent_id = $torrent->id;

    $callback[] = "d" . Bcode::benc_str("interval") . "i" . (60 * 30) . "e" . Bcode::benc_str("peers") . "l";

    $totpeers = $torrent->seeders + $torrent->leechers;

    $peer = new DB("peers");
    $peer->setCols($peer_cols);
    $peer->setColPrefix("peer_");

    if ($totpeers > $readsize) {
        $peer->setSort("RAND()");
        $peer->setLimit($readsize);
    }
    $peer->select("peer_torrent = '" . $torrent->id . "'");

    while ($peer->nextRecord()) {
        $peer->peer_id = str_pad($peer->peer_id, 20);
        if ($peer->peer_id === $data['peer_id']) {
            $self = $peer;
            continue;
        }
        $callback[] = "d" . Bcode::benc_str("ip") . Bcode::benc_str($peer->ip);
        if (!$_GET['no_peer_id']) {
            $callback[] = Bcode::benc_str("peer id") . Bcode::benc_str($peer->peer_id);
        }
        $callback[] = Bcode::benc_str("port") . "i" . $peer->port . "e" . "e";
    }

    $callback[] = "ee";

    $self_query = "peer_torrent = '" . $torrent_id . "' AND peer_passkey = '" . $data['passkey'] . "'";

    if (!isset($self)) {

        $db = new DB("peers");
        $db->setCols($peer_cols);
        $db->setColPrefix("peer_");
        $db->select($self_query);
        if ($db->numRows()) {
            $db->nextRecord();
            $self = $db;
            $data['user_id'] = $self->userid;
        }
    }

    $torrent_query = array();

    if (!isset($self)) {
        $db = new DB("peers");
        $db->select("peer_torrent = '" . $torrent->id . "' AND peer_passkey = '" . $data['passkey'] . "'");

        if ($db->numRows() >= 1 && !$data['seeder'])
            throw new Exception("Error 8: Connection limit exceeded!");

        if ($db->numRows() >= 3 && $data['seeder'])
            throw new Exception("Error 9: Connection limit exceeded!");
    }else {
        $uploaded = max(0, $data['uploaded'] - $self->uploaded);
        $downloaded = max(0, $data['downloaded'] - $self->downloaded);


        if ($uploaded > 0 || $downloaded > 0) {
            if ($torrent->freeleech)
                $user->query("UPDATE {PREFIX}users SET user_uploaded = user_uploaded + $uploaded WHERE user_id='" . $user->id . "'");
            else
                $user->query("UPDATE {PREFIX}users SET user_uploaded = user_uploaded + $uploaded, user_downloaded = user_downloaded + $downloaded WHERE user_id='" . $user->id . "'");
        }
    }

    if (isset($self) && $data['event'] == "stopped") {
        $db = new DB("peers");
        $db->delete($self_query);
        if ($data['seeder']) {
            if ($torrent->seeders != 0)
                $torrent_query[] = "torrent_seeders = torrent_seeders - 1";
        } else {
            if ($torrent->leechers != 0)
                $torrent_query[] = "torrent_leechers = torrent_leechers - 1";
        }
    } else {

        if ($data['event'] == "completed")
            $torrent_query[] = "torrent_times_completed = torrent_times_completed + 1";

        if (isset($self)) {
            $db = new DB();
            $db->query("UPDATE {PREFIX}peers SET peer_uploaded = " . $data['uploaded'] . ", peer_downloaded = " . $data['downloaded'] . ", peer_to_go = " . $data['left'] . ", peer_seeder = '" . $data['seeder'] . "', peer_last_action = '" . time() . "' WHERE $self_query");
            if ($self->seeder != $data['seeder']) {
                if ($data['seeder']) {
                    $torrent_query[] = "torrent_seeders = torrent_seeders + 1";
                    if ($torrent->leechers != 0)
                        $torrent_query[] = "torrent_leechers = torrent_leechers - 1";
                } else {
                    if ($torrent->seeders != 0)
                        $torrent_query[] = "torrent_seeders = torrent_seeders - 1";
                    $torrent_query[] = "torrent_leechers = torrent_leechers + 1";
                }
            }
        } else {
            if ($data['event'] != "started")
                throw new Exception("Peer not found");

            if (blacklist($data['port']))
                throw new Exception("Port " . $data['port'] . " is blacklisted");

            $socket = @fsockopen($data['ip'], $data['port'], $errno, $errstr, 5);
            if (!$socket) {
                $data['connectable'] = false;
            } else {
                $data['connectable'] = true;
                @fclose($socket);
            }

            $db = new DB("peers");
            $db->setColPrefix("peer_");
            $db->torrent = $torrent_id;
            $db->userid = $user->id;
            $db->peer_id = $data['peer_id'];
            $db->ip = $data['ip'];
            $db->port = $data['port'];
            $db->uploaded = 0;
            $db->downloaded = 0;
            $db->to_go = $data['left'];
            $db->seeder = $data['seeder'];
            $db->started = time();
            $db->last_action = time();
            $db->passkey = $data['passkey'];
            $db->connectable = $data['connectable'];
            $db->insert();

            if ($data['seeder'])
                $torrent_query[] = "torrent_seeders = torrent_seeders + 1";
            else
                $torrent_query[] = "torrent_leechers = torrent_leechers + 1";
        }
    }

    if ($data['seeder']) {
        $torrent_query[] = "torrent_visible = '1'";
        $torrent_query[] = "torrent_last_action = '" . time() . "'";
    }

    $db = new DB();
    if (count($torrent_query))
        $db->query("UPDATE {PREFIX}torrents SET " . implode(", ", $torrent_query) . " WHERE torrent_id = '$torrent_id'");

    Bcode::benc_resp_raw(implode("", $callback));
} Catch (Exception $e) {
    Bcode::benc_resp(array('failure reason' => array('type' => 'string', 'value' => $e->getMessage())));
    exit();
}
?>