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
    $db = new DB("files");
    $db->setColPrefix("file_");
    $db->select("file_id = '" . $db->escape($_GET['id']) . "'");
    $db->nextRecord();
    $ext = end(explode(".", $db->name));
    $file = "files/" . $db->id . "." . $ext;

    switch ($ext) {
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            $image = "files/" . $db->id . "." . $ext;
            $isimage = true;
            break;

        default:
            if (file_exists(PATH_ROOT . "siteadmin/images/filetypes/$ext.png"))
                $image = "siteadmin/images/filetypes/$ext.png";
            else
                $image = "siteadmin/images/filetypes/unknown.png";
            $isimage = false;
            break;
    }

    $acl = new Acl($db->userid);
    ?>
    <div class="col_50">

        <fieldset style="min-height:84px;">

            <legend>File information</legend>

            <strong>Name</strong>: <span class="selected"><?php echo $db->name ?><br />
                <strong>Uploaded</strong>: <?php echo get_date($db->added); ?><br>
                <strong>By</strong>: <?php echo $acl->name; ?><br>
                </fieldset>
                <div style="float:left; width:100%;">
                    <fieldset>
                        <legend>Options</legend>
                        <img src="siteadmin/images/icons/impt_16.png" alt="" title="">
                        <a style="margin-right: 10px;" href="<?php echo CMS_URL . $file ?>">Download</a>
                        <img src="siteadmin/images/icons/trash_16.png" alt="" title="">
                        <a style="margin-right: 10px;" href="<?php echo page("admin", "files", "delete", "", "", "id=" . $db->id) ?>">Delete</a>
                    </fieldset>

                </div>
                <div id="imagePreview" style="float:left; width:100%;">
                    <img id="editImage" src="<?php echo $image; ?>"/>
                </div>
                </div>
                <?php
            } Catch (Exception $e) {
                echo error(_t($e->getMessage()));
            }
            ?>
