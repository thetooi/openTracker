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


if (isset($_GET['sort']) && isset($_GET['type'])) {
    $column = '';
    $ascdesc = '';
    $array = array("name", "size", "added", "leechers", "seeders");

    if (in_array($_GET['sort'], $array))
        $column = $_GET['sort'];

    switch ($_GET['type']) {
        case 'asc':
            $ascdesc = "ASC";
            $linkascdesc = "asc";
            break;
        case 'desc':
            $ascdesc = "DESC";
            $linkascdesc = "desc";
            break;
        default: $ascdesc = "DESC";
            $linkascdesc = "desc";
            break;
    }

    $orderby = "torrent_" . $db->escape($column) . " " . $db->escape($ascdesc);
    $pager_add .= "&sort=" . $db->escape($_GET['sort']) . "&type=" . $db->escape($linkascdesc);
} else {
    $orderby = "torrent_added DESC";
}

$order_link = (isset($_GET['type']) && $_GET['type'] == 'desc') ? 'asc' : 'desc';

$count_get = 0;
$oldlink = $char = $description = $type = $sort = $row = '';
foreach ($_GET as $get_name => $get_value) {
    $get_name = strip_tags(str_replace(array("\"", "'"), array("", ""), $get_name));
    $get_value = strip_tags(str_replace(array("\"", "'"), array("", ""), $get_value));
    if ($get_name != "sort" && $get_name != "type" && $get_name != "application" && $get_name != "action" && $get_name != "var_a" && $get_name != "var_b" && $get_name != "var_c") {
        if ($count_get > 0) {
            $oldlink = $oldlink . "&amp;" . $get_name . "=" . $get_value;
        } else {
            $oldlink = ($oldlink) . $get_name . "=" . $get_value;
        }
        $count_get++;
    }
}

if ($count_get > 0) {
    $oldlink = $oldlink . "&amp;";
}
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
                    <label for="cat_<?php echo $cat->id ?>"><a href="<?php echo page("torrent", "browse", "", "", "", "c" . $cat->id . "=1") ?>"><img src="images/categories/<?php echo $cat->icon; ?>" /></a><br />
                        <input type="checkbox" name="c<?php echo $cat->id; ?>" id="cat_<?php echo $cat->id; ?>" value="1" <?php echo $sel; ?> />
                    </label>
                </td>
            <?php }
            ?>
        </table>
    </form>
</center>

<?php
$perpage = ($acl->torrents_perpage != 0) ? $acl->torrents_perpage : 50;
$db->select(implode(" AND ", $where) . "");
$pager = new Pager;
$pager->perpage = $perpage;
$pager->count = $db->numRows();
$pager->href = array("torrent", "browse");
$pager->args = $pager_add;
$pager->build();

echo $pager->pager_top;
?>
<table id="browse" class="forum" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <td width="40px" class="border-bottom">

            </td>
            <td width="50%" class="border-bottom">
                <a href="<?php echo page("torrent", "browse", "", "", "", "{$oldlink}sort=name&amp;type={$order_link}") ?>"><b><?php echo _t("Name"); ?></b></a>
            </td>
            <td class="border-right border-bottom">
            </td>
            <td class="border-right border-bottom" align="center">
                <a href="<?php echo page("torrent", "browse", "", "", "", "{$oldlink}sort=size&amp;type={$order_link}") ?>"><b><?php echo _t("Size"); ?></b></a>
            </td>
            <td class="border-right border-bottom" align="center">
                <a href="<?php echo page("torrent", "browse", "", "", "", "{$oldlink}sort=added&amp;type={$order_link}") ?>"><b><?php echo _t("Uploaded"); ?></b></a>
            </td>
            <td class="border-bottom" align="center">
                <a href="<?php echo page("torrent", "browse", "", "", "", "{$oldlink}sort=seeders&amp;type={$order_link}") ?>"><b><img src="images/icons/up.gif" title="seeders"></b></a>
            </td>
            <td class="border-right border-bottom" align="center">
                <a href="<?php echo page("torrent", "browse", "", "", "", "{$oldlink}sort=leechers&amp;type={$order_link}") ?>"><b><img src="images/icons/down.gif" title="leechers"></b></a>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php
        $db = new DB("torrents");
        $db->setLimit($pager->limit);
        $db->setSort($orderby);

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
                </td>
                <td class="border-right border-bottom" align="center">
                    <?php echo bytes($db->torrent_size); ?>
                </td>
                <td class="border-right border-bottom" align="center">
                    <?php echo str_replace(" ", "<br />", date("Y-m-d H:i", $db->torrent_added)) ?>
                </td>
                <td class="border-bottom" align="center">
                    <?php echo $db->torrent_seeders ?>
                </td>
                <td class="border-right border-bottom" align="center">
                    <?php echo $db->torrent_leechers ?>
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
echo $pager->pager_bottom;
?>