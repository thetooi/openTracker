<?php

try {
    $passkey = (isset($_GET["passkey"]) ? $_GET["passkey"] : '');
    $feed = (isset($_GET["type"]) && $_GET['type'] == 'dl' ? 'dl' : 'web');
    $cats = (isset($_GET["cats"]) ? $_GET["cats"] : "");

    if (!isset($_GET['passkey']) || !isset($_GET["type"]) || !isset($_GET["cats"]))
        throw new Exception("missing data");

    $parts = array();
    if (!preg_match("/[0-9a-fA-F]{32}/", $_GET['passkey'], $parts))
        throw new Exception("invalid passkey");

    $pref = new Pref("website");


    $cats = explode(",", $cats);

    foreach ($cats as $cat) {
        if (!intval($cat))
            throw new Exception("invalid id");
    }

    $where = array();
    $order = "torrent_added DESC";
    if (count($cats) > 0) {
        $wherecatina = array();
        foreach ($cats as $id) {
            $wherecatina[] = $id;
        }
        if (count($wherecatina) > 1)
            $wherecatin = implode(",", $wherecatina);
        elseif (count($wherecatina) == 1 && $wherecatina[0] != "")
            $where[] = "torrent_category = $wherecatina[0]";

        if (isset($wherecatin))
            $where[] = "torrent_category IN(" . $wherecatin . ")";
    }

    $xml = new SimpleXMLElement("<rss></rss>");
    $xml->addAttribute("version", "0.91");

    $channel = $xml->addChild("channel");
    $channel->addChild("title", $pref->name);
    $channel->addChild("link", CMS_URL);
    $channel->addChild("description", "");
    $channel->addChild("language", "en-usde");
    $channel->addChild("copyright", "");
    $icon = $channel->addChild("image");
    $icon->addChild("title", $pref->name);
    $icon->addChild("url", CMS_URL . "favicon.ico");
    $icon->addChild("link", CMS_URL);
    $icon->addChild("width", "16");
    $icon->addChild("height", "16");
    $icon->addChild("description", "");

    $db = new DB("torrents");
    $db->setColPrefix("torrent_");
    $db->query("SELECT * FROM {PREFIX}torrents WHERE " . implode(" OR ", $where) . " ORDER BY $order LIMIT 15");


    while ($db->nextRecord()) {
        $link = ($feed == "dl" ? page("torrent", "download", "", "", "", "torrent=" . $db->id . "&passkey=" . $passkey) : page("torrent", "details", "", "", "", "torrent=" . $db->id));
        $link = htmlentities($link);

        $item = $channel->addChild("item");
        $item->addChild("title", $db->name);
        $item->addChild("link", $link);
        $item->addChild("description", $db->nfo);
    }

    header("Content-Type: application/xml");
    die($xml->asXML());
} Catch (Exception $e) {
    echo $e->getMessage();
}
?>