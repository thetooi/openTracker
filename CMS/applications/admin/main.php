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

try {

    $this->setTitle("Admin Panel");
    $this->setSidebar(true);

    $acl = new Acl(USER_ID);

    if (!$acl->Access("x"))
        throw new Exception("access denied");

    echo "<h4>" . _t("Admin panel") . "</h4>";

    if (file_exists(PATH_ROOT . "setup/index.php"))
        echo notice("Your setup folder should be removed!");
    ?>
    <table width="100%" cellpadding="10">
        <tr>
            <td align="center">
                <a href="<?php echo page("admin", "settings"); ?>"><img src="images/admin/settings.png"><br />
                    <?php echo _t("Settings") ?></a>
            </td>
            <td align="center">
                <a href="<?php echo page("admin", "news"); ?>"><img src="images/admin/news.png"><br />
                    <?php echo _t("News") ?></a>
            </td>
            <td align="center">
                <a href="<?php echo page("admin", "navigation"); ?>"><img src="images/admin/navigation.png"><br />
                    <?php echo _t("Navigation") ?></a>
            </td>
            <td align="center">
                <a href="<?php echo page("admin", "forum"); ?>"><img src="images/admin/forum.png"><br />
                    <?php echo _t("Forum") ?></a>
            </td>
        </tr>

        <tr>
            <td align="center">
                <a href="<?php echo page("admin", "members"); ?>"><img src="images/admin/members.png"><br />
                    <?php echo _t("Members") ?></a>
            </td>
            <td align="center">
                <a href="<?php echo page("admin", "groups"); ?>"><img src="images/admin/groups.png"><br />
                    <?php echo _t("Groups") ?></a>
            </td>
            <td align="center">
                <a href="<?php echo page("admin", "addons"); ?>"><img src="images/admin/addons.png"><br />
                    <?php echo _t("Addons") ?></a>
            </td>
            <td align="center">
                <a href="<?php echo page("admin", "translations"); ?>"><img src="images/admin/translations.png" width="64px" height="64px"><br />
                    <?php echo _t("Translations") ?></a>
            </td>
        </tr>
        <tr>
            <td align="center">
                <a href="<?php echo page("admin", "documents"); ?>"><img src="images/admin/documents.png"><br />
                    <?php echo _t("FAQ / Rules") ?></a>
            </td>
            <td align="center">
                <a href="<?php echo page("admin", "categories"); ?>"><img src="images/admin/categories.png"><br />
                    <?php echo _t("Categories") ?></a>
            </td>
            <td align="center">
                <a href="<?php echo page("admin", "widgets"); ?>"><img src="images/admin/widgets.png" width="64px"><br />
                    <?php echo _t("Widgets") ?></a>
            </td>
            <td align="center">
                <a href="<?php echo page("admin", "support"); ?>"><img src="images/admin/support.png" width="64px"><br />
                    <?php echo _t("Support") ?></a>
            </td>
        </tr>
        <tr>
            <td align="center">
                <a href="<?php echo page("admin", "mysql"); ?>"><img src="images/admin/mysql.png"><br />
                    <?php echo _t("mySQL") ?></a>
            </td>
        </tr>
    </table>

    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>