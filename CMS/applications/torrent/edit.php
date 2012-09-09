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
if (!defined("INCLUDED"))
    die("Access denied");

try {

    $this->setTitle("Edit Torrent");

    $acl = new Acl(USER_ID);

    if (!isset($_GET['torrent']))
        throw new Exception("Missing torrent id");

    $tid = $_GET['torrent'];

    if (!intval($tid))
        throw new Exception("Invalid torrent id");

    if (isset($_POST['save'])) {
        try {
            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("Wrong secured token");


            $db = new DB("torrents");
            $db->torrent_imdb = $_POST['imdb'];
            $db->torrent_youtube = $_POST['youtube'];
            $db->torrent_nfo = $_POST['nfo'];
            $db->torrent_category = $_POST['type'];
            if ($acl->Access("x"))
                $db->torrent_freeleech = (isset($_POST['freeleech']) ? true : false);
            $db->update("torrent_id = '" . $db->escape($tid) . "'");


            if (isset($_POST['imdb']) && !empty($_POST['imdb']))
                $link = $_POST['imdb'];
            else
                $link = $_POST['nfo'];

            $m = preg_match("/tt\\d{7}/", $link, $ids);
            if ($m) {
                $link = "http://www.imdb.com/title/" . $ids[0];

                $db = new DB("torrents");
                $db->torrent_imdb = $link;
                $db->update("torrent_id = '" . $db->escape($tid) . "'");

                $db = new DB("torrents_imdb");
                $db->delete("imdb_torrent = '" . $db->escape($tid) . "'");

                preg_match("#tt(?P<imdbId>[0-9]{7,7})#", $link, $matches);
                if (count($matches) == 0)
                    continue;
                $thenumbers = $matches['imdbId'];
                include(PATH_LIBRARY . "imdb/imdb.class.php");
                $movie = new imdb($thenumbers);
                $movieid = $thenumbers;
                $movie->setid($movieid);
                $gen = $movie->genres();
                $plotoutline = $movie->plotoutline();
                $mvrating = $movie->rating();
                $photo_url = $movie->photo_localurl();
                $db = new DB("torrents_imdb");
                $db->setColPrefix("imdb_");
                $db->torrent = $tid;
                $db->genres = implode(", ", $gen);
                $db->plot = $plotoutline;
                $db->title = $movie->title();
                $db->image = $photo_url;
                $db->rating = $mvrating;
                $db->insert();
            }

            echo Notice(_t("Torrent successfully saved"));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }

    $db = new DB("torrents");
    $db->select("torrent_id = '" . $tid . "'");
    $db->nextRecord();

    $show = false;

    if ($db->torrent_userid == $acl->id)
        $show = true;

    if ($acl->Access("x"))
        $show = true;

    if (!$show)
        throw new Exception("Access Denied");
    ?>

    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <center>
            <table>
                <?php
                $cat = new DB("categories");
                $cat->setColPrefix("category_");
                $cat->select();
                while ($cat->nextRecord()) {
                    $sel = $db->torrent_category == $cat->id ? " CHECKED" : "";
                    ?>
                    <td align="center">
                        <label for="cat_<?php echo $cat->id ?>"><img src="images/categories/<?php echo $cat->icon; ?>" /><br />
                            <input type="radio" name="type" id="cat_<?php echo $cat->id; ?>" value="<?php echo $cat->id ?>" <?php echo $sel; ?> />
                        </label>
                    </td>
                <?php }
                ?>
            </table>
        </center>
        <table width="100%" border="0px" cellspacing="0" cellpadding="10">
            <tbody>
                <tr>
                    <td class="heading" valign="top" align="right"><b><?php echo _t("IMDB-link"); ?></b></td>
                    <td><input type="text" name="imdb" value="<?php echo $db->torrent_imdb ?>" size="60"><br>
                        <font class="small" size="1">
                        (<?php echo _t("Link shall only be pointed to valid imdb") ?>)
                        <br><?php echo _t("Example") ?>: http://www.imdb.com/title/tt1201607/</font></td>
                </tr>
                <tr>
                    <td class="heading" valign="top" align="right"><b><?php echo _t("Youtube-link"); ?></b></td>
                    <td><input type="text" name="youtube" value="<?php echo $db->torrent_youtube ?>" size="60"><br>
                        <font class="small" size="1">
                        (<?php echo _t("Link shall only be pointed to trailer at Youtube."); ?>)
                        <br><?php echo _t("Example") ?>: http://www.youtube.com/watch?v=9iEQdTFb2Rw"</font></td>
                </tr>

                <tr>
                    <td class="heading" valign="top" align="right"><b><?php echo _t("NFO"); ?></b></td>
                    <td><textarea cols="70" name="nfo" rows="15"><?php echo $db->torrent_nfo ?></textarea></td>
                </tr>
                <?php
                if ($acl->Access("x")) {
                    ?>
                    <tr>
                        <td class="heading" width="120px" valign="top" align="right"><b><?php echo _t("Additional"); ?></b></td>
                        <td valign="top" align="left">
                            <label><input type="checkbox" name="freeleech" <?php echo ($db->torrent_freeleech) ? "CHECKED" : "" ?> /><?php echo _t("Freeleech"); ?></label>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td align="right" colspan="1"><input type="submit" name="save" value="<?php echo _t("Save Torrent"); ?>"></td>
                </tr>
            </tbody>
        </table>
    </form>

    <?php
} Catch (Exception $e) {
    echo Notice(_t($e->getMessage()));
}
?>