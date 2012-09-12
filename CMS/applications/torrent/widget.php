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

<h4><?php echo _t("Latest Torrents") ?></h4>

<?php
$db = new DB("torrents");
$db->setSort("torrent_added DESC");
$db->setLimit("6");
$db->select("torrent_visible = '1'");
if ($db->numRows()) {
    ?>
    <table width="100%">
        <?php
        while ($db->nextRecord()) {
            ?>
            <tr>
                <td>
                    <a href="<?php echo page("torrent", "details", "", "", "", "id=" . $db->torrent_id) ?>"><?php echo trimstr($db->torrent_name, 37); ?></a>
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