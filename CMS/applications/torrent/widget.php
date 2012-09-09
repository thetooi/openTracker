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
?>

<h4><?php echo _t("Latest Torrents") ?></h4>

<?php
$db = new DB("torrents");
$db->setSort("torrent_added DESC");
$db->setLimit("6");
$db->join("left", "{PREFIX}categories", "category_id", "torrent_category");
$db->select("torrent_visible = '1'");
if ($db->numRows()) {
    ?>
    <table width="100%" cellpadding="3" cellspacing="3">
        <?php
        while ($db->nextRecord()) {
            ?>
            <tr>
                <td style="padding:0px;">
                    <img src="images/categories/<?php echo $db->category_icon ?>"  />
                </td>
                <td style="padding:0px; padding-left: 2px;">
                    <a href="<?php echo page("torrent", "details", "", "", "", "id=" . $db->torrent_id) ?>"><?php echo trimstr($db->torrent_name, 38); ?></a>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
} else {
    echo notice(_t("No Torrents found"));
}
?>