<?php

$this->setTitle("Torrent Details");

try {

    if (!isset($_GET['id']))
        throw new Exception("missing id");

    if(!intval($_GET['id']))
        throw new Exception("invalid id");
    
    $db = new DB("torrents_imdb");
    $db->select("imdb_torrent = '" . $db->escape($_GET['id']) . "'");

    $tpl = new Template(PATH_APPLICATIONS . "torrent/tpl/");
    if ($db->numRows())
        $tpl->loadFile("details_imdb.php");
    else
        $tpl->loadFile("details.php");

    $tpl->build();
} Catch (Exception $e) {
   echo error(_t($e->getMessage()));
}
?>