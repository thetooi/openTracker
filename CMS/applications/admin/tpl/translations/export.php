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

    if (!$this->lang_id)
        throw new Exception("Missing lang id");

    $db = new DB("system_languages");
    $db->select("language_id = '" . $db->escape($this->lang_id) . "'");

    if (!$db->numRows())
        throw new Exception("Could not find lang id.");

    $db->nextRecord();
    $out = "";
    $trans = new DB("translation");
    $trans->select("translation_lang_id='" . $db->language_id . "'");
    while ($trans->nextRecord()) {
        $out .= $trans->translation_lang_id . ":::" . $trans->translation_phrase . ":::" . $trans->translation_phrase_translated . ";;;";
    }

    header('Content-Disposition: attachment; filename="' . $this->lang_id . '.otlang"');
    header("Content-Type: application/ot-lang");

    $xml = new SimpleXMLElement("<language></language>");
    $xml->addChild("name", $db->language_name);
    $xml->addChild("flag", $db->language_flag);
    $xml->addChild("lang_id", $db->language_id);
    $xml->addChild("content", base64_encode($out));
    die(base64_encode($xml->asXML()));
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
