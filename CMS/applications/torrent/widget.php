<h4><?php echo _t("Latest Torrents") ?></h4>

<?php
$db = new DB("torrents");
$db->setSort("torrent_added DESC");
$db->setLimit("6");
$db->select("torrent_visible = '1'");
if ($db->numRows()) {
    ?>
    <table>
        <?php
        while ($db->nextRecord()) {
            ?>
            <tr>
                <td>
                    <a href="<?php echo page("torrent", "details", "", "", "", "id=" . $db->torrent_id) ?>"><?php echo $db->torrent_name; ?></a>
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