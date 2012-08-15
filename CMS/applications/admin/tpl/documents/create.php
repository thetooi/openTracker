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

if (isset($_POST['create'])) {
    try {

        $type = $_POST['type'];

        switch ($type) {
            case 'faq':
                $db = new DB("faqs");
                $db->faq_lang = $_POST['lang'];
                $db->faq_content = $_POST['content'];
                $db->faq_edited = time();
                $db->insert();
                header("location: " . page("admin", "documents"));
                break;

            case 'rules':
                $db = new DB("rules");
                $db->rule_lang = $_POST['lang'];
                $db->rule_content = $_POST['content'];
                $db->rule_edited = time();
                $db->insert();
                header("location: " . page("admin", "documents"));
                break;
        }
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>
<form method="post">
    <h4>Create new document</h4>
    <table>
        <tr>
            <td width="20px"><?php echo _t("Type") ?></td>
            <td width="70px"><select name="type"><option value="faq"><?php echo _t("FAQ") ?></option><option value="rules"><?php echo _t("Rules") ?></option></select></td>
            <td width="50px"><?php echo _t("Language") ?></td>
            <td><select name="lang"><?php echo getLanguages(); ?></select></td>
        </tr>
        <tr>
            <td colspan="6"><?php echo bbeditor("content", 25, 110); ?>
            </td>
        </tr>
        <tr>
            <td colspan="5">
                <input type="submit" name="create" value="<?php echo _t("Create document") ?>" />
            </td>
        </tr>
    </table>
</form>