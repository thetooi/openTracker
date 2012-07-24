<?php
try {

    $acl = new Acl(USER_ID);
    if (!$acl->Access("x"))
        throw new Exception("Access denied");

    if (!isset($_GET['id']))
        throw new Exception("Missing news id");

    if (isset($_GET['confirm'])) {
        ?>
        <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
            <center>Are you sure you wish to delete this?<br /><br />
                <a href="<?php echo page("torrent", "delete", "", "", "", "id=" . $_GET['id']) ?>"><span class="btn red">Yes</span></a> 
                <a href="<?php echo page("torrent", "details", "", "", "", "id=" . $_GET['id']) ?>"><span class="btn">No</span></center></a>
        </div>
        <?
    } else {
        $db = new DB;
        $db->query("DELETE FROM {PREFIX}torrents WHERE torrent_id = '" . $db->escape($_GET['id']) . "'");
        $db->query("DELETE FROM {PREFIX}torrents_comments WHERE comment_torrent = '" . $db->escape($_GET['id']) . "'");
        $db->query("DELETE FROM {PREFIX}torrents_imdb WHERE imdb_torrent = '" . $db->escape($_GET['id']) . "'");
        unlink(PATH_TORRENTS . $_GET['id'] . ".torrent");
        header("location: " . page("torrent", "browse"));
    }
} catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
