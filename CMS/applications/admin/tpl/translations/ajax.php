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


include("../../../../../init.php");

try {

    $acl = new Acl(USER_ID);

    if (!$acl->Access("x"))
        throw new Exception("Access denied");

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'save':

                $phrase = $_POST['text'];
                $translation = $_POST['translation'];
                $lang_id = $_POST['language'];

                $db = new DB("translation");
                $db->select("translation_lang_id = '" . $lang_id . "' AND translation_phrase='" . $db->escape($phrase) . "'");
                if ($db->numRows() == 1) {
                    $c = new DB("translation");
                    $c->translation_phrase_translated = $translation;
                    $c->update("translation_lang_id = '" . $lang_id . "' AND translation_phrase='" . $db->escape($phrase) . "'");
                    echo Notice("Saved");
                } else {
                    $c = new DB("translation");
                    $c->translation_lang_id = $lang_id;
                    $c->translation_phrase = $phrase;
                    $c->translation_phrase_translated = $translation;
                    $c->insert();
                    echo Notice("insert " . $lang_id . " " . $phrase . "=>" . $translation);
                }

                break;
        }
    }
} Catch (Exception $e) {
    echo _t($e->getMessage());
}
?>
