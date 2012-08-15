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

?>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>
<?php
try {
    if (!isset($_GET['id']))
        throw new Exception("Missing ID");

    $id = $_GET['id'];

    $db = new DB("torrents");
    $db->join("LEFT", "{PREFIX}torrents_imdb", "torrent_id", "imdb_torrent");
    $db->select("torrent_id = '" . $id . "'");

    if (!$db->numRows())
        throw new Exception("Torrent not found");
    $db->nextRecord();
    $this->setTitle($db->torrent_name);
    $torrent = new Torrent($db->torrent_id);
    $user = new Acl(USER_ID);
    $acl = new Acl($db->torrent_userid);

    $edit = false;

    if ($db->torrent_userid == USER_ID)
        $edit = true;

    if ($user->Access("x"))
        $edit = true;

    if ($edit)
        echo "<a href='" . page("torrent", "edit", "", "", "", "torrent=" . $db->torrent_id) . "'><span class='btn'>" . _t("Edit Torrent") . "</span></a>";

    if ($user->Access("x"))
        echo "<a href='" . page("torrent", "delete", "", "", "", "id=" . $db->torrent_id . "&confirm") . "'><span class='btn red'>" . _t("Delete Torrent") . "</span></a>";

    if ($edit)
        echo "<br /> <br />";
    ?>

    <table width="100%">
        <td>
        <td style="width: 150px;" valign="top">
            <a href="<?php echo $db->imdb_image ?>" title="<?php echo $db->imdb_title ?>" /><img src="<?php echo $db->imdb_image ?>" width="150px" /></a>
        </td>
    </td>
    <td>
        <table width="100%" class="details" cellspacing="0" cellpadding="5">
            <tbody>
            <h4><?php echo $db->imdb_title ?></h4>
            <?php echo strip_tags($db->imdb_plot) ?>
            <?php
            if ($db->torrent_youtube != "") {
                ?>
                <tr>
                    <td colspan="2">
                        <object type="application/x-shockwave-flash" style="width:500px; height:260px;" data="http://www.youtube.com/v/<?php echo findyoutube($db->torrent_youtube); ?>?showsearch=0&amp;showinfo=0&amp;version=3&amp;modestbranding=1&amp;fs=1" allowscriptaccess="always" allowfullscreen="true">
                            <param name="movie" value="http://www.youtube.com/v/<?php echo findyoutube($db->torrent_youtube); ?>?showsearch=0&amp;showinfo=0&amp;version=3&amp;modestbranding=1&amp;fs=1"><param name="allowFullScreen" value="true">
                            <param name="allowFullScreen" value="true">
                            <param name="allowscriptaccess" value="always">
                        </object>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr><td width = "100px" align = "center"><img src = "images/categories/<?php echo $torrent->category() ?>"></td><td></td></tr>
            <tr><td align = "right"><b>Download</b></td><td><a href="<?php echo page("torrent", "download", "", "", "", "torrent=" . $db->torrent_id); ?>"><?php echo $db->torrent_name
            ?></a></td></tr>
            <tr><td align="right" valign="top"><b>Info</b></td><td><?php echo htmlformat($db->torrent_nfo, true) ?></td></tr>
            <tr><td align="right"><b>Size</b></td><td><?php echo bytes($db->torrent_size) ?></td></tr>
            <tr><td align="right"><b>Files</b></td><td><a href="<?php echo page("torrent", "files", "", "", "", "torrent=" . $db->torrent_id); ?>"><?php echo $db->torrent_numfiles ?></a></td></tr>
            <tr><td align="right"><b>Upped by</b></td><td>
                    <?php
                    if (!$acl->anonymous || $user->Access("x"))
                        echo "<a href='" . page("profile", "view", $acl->name) . "'>" . $acl->name . "</a>";
                    else
                        echo _t("Anonymous");
                    ?>
                </td></tr>
            <tr><td align="right"><b>Peers</b></td><td>
                    <a href="<?php echo page("torrent", "peers", "", "", "", "torrent=" . $db->torrent_id); ?>">
                        <?php echo $db->torrent_seeders; ?> seeder(s), <?php echo $db->torrent_leechers ?> leecher(s) = <?php echo ($db->torrent_seeders + $db->torrent_leechers) ?> peer(s) total
                    </a>
                </td></tr>
            </tbody>
        </table>
    </td>
    </table>
    <br />
    <h4><?php echo _t("Comments"); ?></h4>
    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <?php echo bbeditor("content") ?>
        <input type="submit" name="comment" value="<?php echo _t("Comment"); ?>" />
    </form>
    <div id="conv">
        <?php
        if (isset($_POST['comment'])) {
            try {
                if ($_POST['secure_input'] != $_SESSION['secure_token'])
                    throw new Exception("Wrong secured token");

                if (empty($_POST['content']))
                    throw new Exception("cannot post a comment without a content");

                $db = new DB("torrents_comments");
                $db->setColPrefix("comment_");
                $db->user = USER_ID;
                $db->added = time();
                $db->content = $_POST['content'];
                $db->torrent = $id;
                $db->insert();
            } Catch (Exception $e) {
                echo error(_t($e->getMessage()));
            }
        }

        $db = new DB("torrents_comments");
        $db->setColPrefix("comment_");
        $db->setSort("comment_added DESC");
        $db->select("comment_torrent = '" . $db->escape($id) . "'");
        while ($db->nextRecord()) {
            $user = new Acl($db->user);
            ?>
            <div class="item">
                <div class="avatar">
                    <?php echo "<img src='" . $user->avatar() . "' style='max-width:70px'>"; ?>
                </div>
                <div class="message">
                    <b><a href="<?php echo page("profile", "view", strtolower($user->name)) ?>"><?php echo $user->name ?></a></b> <br />
                    <?php echo htmlformat($db->content, true); ?>
                </div>
                <div class="date">
                    <?php echo get_date($db->added) ?>
                </div>
            </div>

            <?php
        }

        echo "</div>";
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
    ?>
