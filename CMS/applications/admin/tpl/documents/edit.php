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

    if (!$this->type)
        throw new Exception("Missing type");

    if (!$_GET['lang'])
        throw new Exception("Missing language");

    if (isset($_POST['save'])) {
        if ($this->type == "faq") {
            $db = new DB("faqs");
            $db->faq_content = $_POST['content'];
            $db->faq_edited = time();
            $db->update("faq_lang = '" . $db->escape($_GET['lang']) . "'");
        }

        if ($this->type == "rules") {
            $db = new DB("rules");
            $db->rule_content = $_POST['content'];
            $db->rule_edited = time();
            $db->update("rule_lang = '" . $db->escape($_GET['lang']) . "'");
        }
        echo notice(_t("Content Saved!"));
    }

    if ($this->type == "faq") {
        $db = new DB("faqs");
        $db->setColPrefix("faq_");
        $db->select("faq_lang = '" . $db->escape($_GET['lang']) . "'");
        if (!$db->numRows())
            throw new Exception("Language not found");
        $db->nextRecord();
    }

    if ($this->type == "rules") {
        $db = new DB("rules");
        $db->setColPrefix("rule_");
        $db->select("rule_lang = '" . $db->escape($_GET['lang']) . "'");
        if (!$db->numRows())
            throw new Exception("Language not found");
        $db->nextRecord();
    }

    if ($this->type == "faq")
        echo "<h4>" . _t("Editing FAQ") . "</h4>";
    else if ($this->type == "rules")
        echo "<h4>" . _t("Editing Rules") . "</h4>";
    ?>

    <form method="post">
        <?
        echo bbeditor("content", 25, 110, $db->content);
        ?><br />
        <input type="submit" name="save" value="<?php echo _t("Save") ?>" />
    </form>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
