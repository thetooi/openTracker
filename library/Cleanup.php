<?php

/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * filename library/Cleanup.php
 * 
 * @author Wuild
 * @package openTracker.Cleanup
 */
class Cleanup {

    /**
     * peers dead time in seconds
     * @var int
     */
    private $deadtime_peers;

    /**
     * torrents dead time in seconds
     * @var int
     */
    private $deadtime_torrents;

    /**
     * Check when the last cleanup was
     * @param boolean $force force a cleanup
     */
    function __construct($force = false) {
        $db = new DB("avps");
        $db->select();
        $doclean = false;
        if (!$db->numRows()) {
            $db->last_cleantime = time();
            $db->insert();
            $doclean = true;
        } else {
            $db->nextRecord();
            $time = time() - 900;
            if ($db->last_cleantime < $time)
                $doclean = true;
        }

        if ($force)
            $doclean = true;

        if ($doclean) {
            set_time_limit(0);
            ignore_user_abort(1);
            $this->deadtime_peers = time() - floor(60 * 30 * 1.3); // 39 minutes.
            $this->deadtime_torrents = time() - floor(60 * 60 * 24 * 3); // 3 Days
            $this->deadtime_users = time() - floor(60 * 60 * 24 * 56); // 56 Days
            $this->torrents();
            $this->groups();
            $this->bonus();
            $db = new DB("avps");
            $db->last_cleantime = time();
            $db->update();
        }
    }

    /**
     * Cleanup torrents. 
     */
    function torrents() {


        $db = new DB;
        // Remove inactive peers
        $db->query("DELETE FROM {PREFIX}peers WHERE peer_last_action < " . $this->deadtime_peers);
        $db->query("UPDATE {PREFIX}torrents SET torrent_visible='0' WHERE torrent_visible='1' AND torrent_last_action < " . $this->deadtime_peers);
        $db->query("DELETE FROM {PREFIX}users WHERE user_last_access < " . $this->deadtime_users . " AND user_status != 1");

        set_time_limit(0);
        ignore_user_abort(1);

        do {



            $db = new DB("torrents");
            $db->setCol("torrent_id");
            $db->select();
            $ar = array();
            while ($db->nextRecord()) {
                $id = $db->torrent_id;
                $ar[$id] = 1;
            }

            if (!count($ar))
                break;

            $dp = @opendir(PATH_TORRENTS);
            if (!$dp)
                break;

            $ar2 = array();
            while (($file = readdir($dp)) !== false) {
                if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
                    continue;
                $id = $m[1];
                $ar2[$id] = 1;
                if (isset($ar[$id]) && $ar[$id])
                    continue;
                $ff = PATH_TORRENTS . "/$file";
                unlink($ff);
            }
            closedir($dp);

            if (!count($ar2))
                break;

            $delids = array();
            foreach (array_keys($ar) as $k) {
                if (isset($ar2[$k]) && $ar2[$k])
                    continue;
                $delids[] = $k;
                unset($ar[$k]);
            }
            if (count($delids))
                mysql_query();
            $db->query("DELETE FROM {PREFIX}torrents WHERE torrent_id IN ('" . join(",", $delids) . "')");

            $db = new DB();
            $db->query("SELECT peer_torrent FROM peers GROUP BY peer_torrent");
            $db->select();
            $delids = array();
            while ($db->nextRecord()) {
                $id = $db->peer_torrent;
                if (isset($ar[$id]) && $ar[$id])
                    continue;
                $delids[] = $id;
            }
            if (count($delids))
                $db->query("DELETE FROM {PREFIX}peers WHERE peer_torrent IN ('" . join(",", $delids) . "')");
        } while (0);

        $torrents = array();
        $db = new DB();
        $db->query("SELECT peer_torrent, peer_seeder, COUNT(*) AS c FROM {PREFIX}peers GROUP BY peer_torrent, peer_seeder");
        while ($db->nextRecord()) {
            if ($db->peer_seeder == "1")
                $key = "torrent_seeders";
            else
                $key = "torrent_leechers";

            $torrents[$db->peer_torrent][$key] = $db->c;
        }

        $fields = explode(":", "torrent_leechers:torrent_seeders");
        $db = new DB();
        $db->query("SELECT torrent_id, torrent_seeders, torrent_leechers FROM {PREFIX}torrents");
        while ($db->nextRecord()) {
            $id = $db->torrent_id;
            if (isset($torrents[$id]))
                $torr = $torrents[$id];
            foreach ($fields as $field) {
                if (!isset($torr[$field]))
                    $torr[$field] = 0;
            }
            $update = array();
            foreach ($fields as $field) {
                if ($torr[$field] != $db->$field)
                    $update[] = "$field = " . $torr[$field];
            }
            if (count($update)){
                $db2 = new DB;
                $db2->query("UPDATE {PREFIX}torrents SET " . implode(",", $update) . " WHERE torrent_id = '$id'");
            }
        }
    }

    /**
     * Cleanup groups 
     */
    function groups() {
        $notif = new notifications_main();
        $db = new DB("groups");
        $db->setSort("group_id ASC");
        $db->select();
        while ($db->nextRecord()) {
            $user = new DB("users");
            $user->select("user_group = '" . $db->group_id . "'");
            while ($user->nextRecord()) {

                $ratio = false;
                if ($user->user_uploaded != "0" && $user->user_downloaded != "0") {
                    $ratio = round($user->user_uploaded / $user->user_downloaded, 2);
                }

                if ($ratio != false && $user->user_uploaded >= $db->group_minupload && $ratio >= $db->group_minratio && $db->group_upgradable == 1 && $user->user_group != $db->group_upgradeto) {
                    $db2 = new DB("users");
                    $db2->user_group = $db->group_upgradeto;
                    $db2->update("user_id = '" . $user->user_id . "'");
                    $notif->add($user->user_id, "system", json_encode(array("type" => "upgrade", "group" => $db->group_upgradeto)));
                }
            }
        }

        $db = new DB("groups");
        $db->setSort("group_id ASC");
        $db->select();
        while ($db->nextRecord()) {
            $user = new DB("users");
            $user->select("user_group = '" . $db->group_id . "'");
            while ($user->nextRecord()) {
                $notif = new notifications_main();
                $ratio = false;
                if ($user->user_uploaded != 0 && $user->user_downloaded != 0) {
                    $ratio = round($user->user_uploaded / $user->user_downloaded, 2);
                }

                if ($ratio != false && $ratio < $db->group_minratio && $db->group_downgradeto != 0 && $user->user_group != $db->group_downgradeto) {
                    $db2 = new DB("users");
                    $db2->user_group = $db->group_downgradeto;
                    $db2->update("user_id = '" . $user->user_id . "'");
                    $notif->add($user->user_id, "system", json_encode(array("type" => "downgrade", "group" => $db->group_downgradeto)));
                }
            }
        }
    }

    /**
     * Give seedbonus to users  
     */
    function bonus() {
        $db = new DB("peers");
        $db->select("peer_seeder = 1 GROUP BY peer_userid");
        if ($db->numRows() > 0) {
            while ($db->nextRecord()) {
                $add = 0.25;
                $db2 = new DB;
                $db2->query("UPDATE {PREFIX}users SET user_bonus = user_bonus + $add WHERE user_id= '" . $db->escape($db->peer_userid) . "' ");
            }
        }
    }

}

?>
