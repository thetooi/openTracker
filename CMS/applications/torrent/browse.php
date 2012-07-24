<?php
$this->setTitle("Browse");

$acl = new Acl(USER_ID);

$db = new DB("torrents");
$db->select("torrent_visible = '1'");

$pager_add = "";
$searchstr = "";
$query_cats = array();
$where = array();

if (isset($_GET['q'])) {
    $searchstr = $db->escape(searchfield($_GET['q']));
    $pager_add .= "&q=" . $searchstr;
    $where[] = "torrent_search_text LIKE '%" . $searchstr . "%'";
}


$cat = new DB("categories");
$cat->setColPrefix("category_");
$cat->setSort("category_name ASC");
$cat->select();
while ($cat->nextRecord()) {
    if (isset($_GET['c' . $cat->id])) {
        $query_cats[] = $cat->id;
        $pager_add .= "&c" . $cat->id . "=1";
    }
}

if (count($query_cats) < 1 && $acl->default_categories != "") {
    $cats = explode(",", $acl->default_categories);
    foreach ($cats as $id) {
        $query_cats[] = $id;
        $pager_add .= "&c" . $id . "=1";
    }
}

if (count($query_cats) > 1) {
    $cats = implode(",", $query_cats);
} elseif (count($query_cats) == 1) {
    $where[] = "torrent_category = $query_cats[0]";
}

if (isset($cats))
    $where[] = "torrent_category IN(" . implode(",", $query_cats) . ")";

if (!isset($_GET['incl']))
    $where[] = "torrent_visible = '1'";

if (isset($_GET['incl']) && $_GET['incl'] == 0)
    $where[] = "torrent_visible = '1'";
else if (isset($_GET['incl']) && $_GET['incl'] == 1)
    $where[] = "torrent_visible = '0'";
?>

<center>
    <form method="GET">
        <?php
        if (!CLEAN_URLS) {
            ?>
            <?php if (isset($this->args['application'])) { ?><input type="hidden" name="application" value="<?php echo $this->args['application'] ?>"><?php } ?>
            <?php if (isset($this->args['action'])) { ?><input type="hidden" name="action" value="<?php echo $this->args['action'] ?>"><?php } ?>
            <?php if (isset($this->args['var_a'])) { ?><input type="hidden" name="var_a" value="<?php echo $this->args['var_a'] ?>"><?php } ?>
            <?php if (isset($this->args['var_b'])) { ?><input type="hidden" name="var_b" value="<?php echo $this->args['var_b'] ?>"><?php } ?>
            <?php if (isset($this->args['var_c'])) { ?><input type="hidden" name="var_c" value="<?php echo $this->args['var_c'] ?>"><?php } ?>
            <?php
        }
        ?>
        <input type="text" name="q" value="<?php echo $searchstr != "" ? $searchstr : "" ?>" size="60" /> 
        <select name="incl">
            <option value="0" <?php echo (isset($_GET['incl']) && $_GET['incl'] == 0) ? "SELECTED" : "" ?>><?php echo _t("Active") ?></option>
            <option value="1" <?php echo (isset($_GET['incl']) && $_GET['incl'] == 1) ? "SELECTED" : "" ?>><?php echo _t("Dead") ?></option>
            <option value="2" <?php echo (isset($_GET['incl']) && $_GET['incl'] == 2) ? "SELECTED" : "" ?>><?php echo _t("All") ?></option>
        </select>
        <input type="submit" value="<?php echo _t("Search") ?>" /><br />
        <table>
            <?php
            $cat = new DB("categories");
            $cat->setColPrefix("category_");
            $cat->select();
            while ($cat->nextRecord()) {
                $sel = isset($_GET['c' . $cat->id]) || in_array($cat->id, $query_cats) ? " CHECKED" : "";
                ?>
                <td align="center">
                    <label for="cat_<?php echo $cat->id ?>"><img src="images/categories/<?php echo $cat->icon; ?>" /><br />
                        <input type="checkbox" name="c<?php echo $cat->id; ?>" id="cat_<?php echo $cat->id; ?>" value="<?php echo $cat->id ?>" <?php echo $sel; ?> />
                    </label>
                </td>
            <?php }
            ?>
        </table>
    </form>
</center>

<?php
$perpage = ($acl->torrents_perpage != 0) ? $acl->torrents_perpage : 50;

$pager = pager($perpage, $db->numrows(), page("torrent", "browse"), $pager_add);
echo $pager['pagertop'];
?>
<table id="browse" class="forum" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <td width="40px" class="border-bottom">

            </td>
            <td width="50%" class="border-bottom">
                <b><?php echo _t("Name"); ?></b>
            </td>
            <td class="border-right border-bottom">
            </td>
            <td class="border-right border-bottom" align="center">
                <b><?php echo _t("Size"); ?></b>
            </td>
            <td class="border-right border-bottom" align="center">
                <b><?php echo _t("Uploaded"); ?></b>
            </td>
            <td class="border-right border-bottom" align="center">
                <b><img src="images/icons/down.gif" title="leechers"></b>
            </td>
            <td class="border-bottom" align="center">
                <b><img src="images/icons/up.gif" title="seeders"></b>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php
        $db = new DB("torrents");
        $db->setLimit($pager['limit']);
        $db->setSort("torrent_added DESC");

        $db->select(implode(" AND ", $where) . "");

        while ($db->nextRecord()) {

            $torrent = new Torrent($db->torrent_id);
            ?>
            <tr>
                <td class="border-bottom">
                    <img src="images/categories/<?php echo $torrent->category(); ?>" />
                </td>
                <td width="50%" class="border-bottom">
                    <a href="<?php echo page("torrent", "details", "", "", "", "id=" . $db->torrent_id); ?>"><?php echo $db->torrent_name; ?></a>
                    <?php echo ($db->torrent_freeleech) ? "<br /><small class='freeleech'>Freeleech</small>" : "" ?>
                </td>
                <td class="border-right border-bottom" align="center">
                    <a href="<?php echo page("torrent", "download", "", "", "", "torrent=" . $db->torrent_id) ?>"><img src="images/icons/download.png" title="<?php echo _t("Download"); ?>" /></a>
    <!--                    <img src="images/icons/bookmark.png">-->
                </td>
                <td class="border-right border-bottom" align="center">
                    <?php echo bytes($db->torrent_size); ?>
                </td>
                <td class="border-right border-bottom" align="center">
                    <?php echo str_replace(" ", "<br />", date("Y-m-d H:i", $db->torrent_added)) ?>
                </td>
                <td class="border-right border-bottom" align="center">
                    <?php echo $db->torrent_leechers ?>
                </td>
                <td class="border-bottom" align="center">
                    <?php echo $db->torrent_seeders ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php
if (!$db->numRows())
    echo "<br /><center>" . _t("No torrents found") . "</center>";
?>
<br />
<?php
echo $pager['pagerbottom'];
?>