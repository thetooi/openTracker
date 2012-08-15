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
<h4><?php echo _t("Import language file") ?></h4>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="lang_file" />
    <input type="submit" name="import" value="<?php echo _t("Import") ?>" />
</form>

<?php
try {
    if (isset($_POST['import'])) {

        if (!isset($_FILES['lang_file']))
            throw new Exception("missing data");

        $filename = $_FILES['lang_file']['name'];
        $ext = end(explode(".", $filename));

        if ($ext != "otlang")
            throw new Exception("Invalid data");

        $data = file_get_contents($_FILES['lang_file']['tmp_name']);
        $data = base64_decode($data);
        $xml = simplexml_load_string($data);

        $db = new DB("system_languages");
        $db->select("language_id = '" . $db->escape($xml->lang_id) . "'");
        if (!$db->numRows()) {
            $db = new DB("system_languages");
            $db->language_id = $xml->lang_id;
            $db->language_name = $xml->name;
            $db->language_flag = $xml->flag;
            $db->language_installed = time();
            $db->insert();
        }else{
            $db = new DB("system_languages");
            $db->language_id = $xml->lang_id;
            $db->language_name = $xml->name;
            $db->language_flag = $xml->flag;
            $db->language_installed = time();
            $db->update("language_id = '" . $db->escape($xml->lang_id) . "'");
        }

        $data = $xml->content;
        $data = base64_decode($data);
        $data = explode(";;;", $data);

        $db = new DB;

        if (!count($data))
            throw new Exception("Error");

        foreach ($data as $string) {
            $string = explode(":::", $string);
            $lang_id = $string[0];
            $phrase = $string[1];
            $phrase_translated = $string[2];
            echo $phrase_translated . "<br />";
            $db->query("DELETE FROM {PREFIX}translation WHERE translation_phrase = '" . $db->escape($phrase) . "' AND translation_lang_id = '" . $db->escape($lang_id) . "'");
            $db = new DB("translation");
            $db->translation_lang_id = $lang_id;
            $db->translation_phrase = $phrase;
            $db->translation_phrase_translated = $phrase_translated;
            $db->insert();
        }
        header("location: " . page("admin", "translations"));
    }
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>