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

$this->setSidebar(true);

?>

<h4><?php echo _t("Generate RSS feed link"); ?></h4>
<form method="POST">
    <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
    <table>
        <?php
        $cat = new DB("categories");
        $cat->setColPrefix("category_");
        $cat->select();
        while ($cat->nextRecord()) {
            $sel = isset($_GET['c' . $cat->id]) ? " CHECKED" : "";
            ?>
            <td align="center">
                <label for="cat_<?php echo $cat->id ?>"><img src="images/categories/<?php echo $cat->icon; ?>" /><br />
                    <input type="checkbox" name="cats[]" id="cat_<?php echo $cat->id; ?>" value="<?php echo $cat->id ?>" <?php echo $sel; ?> />
                </label>
            </td>
        <?php }
        ?>
    </table>
    <input type="radio" name="type" id="type" value="dl" CHECKED> <?php echo _t("Download link") ?> <input type="radio" name="type" value="web" id="type"> <?php echo _t("Web link") ?><br /><br />
    <input type="submit" name="rss" value="<?php echo _t("Get RSS link"); ?>">
</form>

<?php
$this->setTitle("RSS Generator");

if (isset($_POST['rss'])) {
    try {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");

        $acl = new Acl(USER_ID);

        $base = CMS_URL . "rss.php?";
        $link[] = "passkey=" . $acl->passkey;

        $link[] = "type=" . $_POST['type'];

        if (!isset($_POST['cats']))
            throw new Exception("You need to select atleast one category");

        $cats = array();
        foreach ($_POST['cats'] as $cat) {
            $cats[] = $cat;
        }
        $link[] = "cats=" . implode(",", $cats);
        echo "<br />" . page("rss", "feed", "", "", "", implode("&", $link));
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>