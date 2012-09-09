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
    ?>
    <ul class="iconButtons" style="display: block; margin-top: 5px; float:left; width: 100%; ">
        <li><img src="siteadmin/images/icons/new_16.png" alt="" title="">
            <a href="<?php echo page("admin", "files", "upload") ?>">Upload files</a>
        </li>
    </ul>
    <?php
    $db = new DB("files");
    $db->setSort("file_added DESC");
    $db->setColPrefix("file_");
    $db->select();
    while ($db->nextRecord()) {

        $ext = end(explode(".", $db->name));

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                $image = "files/" . $db->id . "." . $ext;
                break;

            default:
                if (file_exists(PATH_ROOT . "siteadmin/images/filetypes/$ext.png"))
                    $image = "siteadmin/images/filetypes/$ext.png";
                else
                    $image = "siteadmin/images/filetypes/unknown.png";
                break;
        }
        ?>
        <div class="thumbnailWrapper">
            <div class="thumbnailImage">
                <a href="<?php echo page("admin", "files", "view", "", "", "id=" . $db->id) ?>">
                    <img src="<?php echo $image; ?>" style="max-width: 100%; max-height: 100%;" title="<?php echo $db->name ?>">
                </a><br />
            </div>
            <ul>
                <li><a href="<?php echo page("admin", "files", "view", "", "", "id=" . $db->id) ?>"><img src="siteadmin/images/icons/edit_16.png" alt="" title=""></a></li>
                <li><a href="<?php echo page("admin", "files", "delete", "", "", "id=" . $db->id) ?>"><img src="siteadmin/images/icons/trash_16.png" alt="" title=""></a></li>
            </ul>
        </div>
        <?php
    }
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
