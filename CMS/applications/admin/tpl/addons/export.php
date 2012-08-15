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

    $not_allowed = array(
        "admin",
        "faq",
        "forums",
        "news",
        "notifications",
        "profile",
        "rss",
        "rules",
        "faq",
        "search",
        "staff",
        "torrent",
        "user"
    );

    if (in_array($this->addon, $not_allowed))
        throw new Exception("Error");

    header("Content-Type: application/xml");


    $folders = makefilelist(PATH_APPLICATIONS . $this->addon . "/", ".|..", true, "folders");
    $files = makefilelist(PATH_APPLICATIONS . $this->addon . "/", ".|..", true);

    $folder_files = array();
    foreach ($folders as $folder) {
        $folder_files[] = array("name" => $folder, "files" => makefilelist(PATH_APPLICATIONS . $this->addon . "/" . $folder . "/", ".|..", true));
    }

    $db = new DB("system");
    $db->select();
    $db->nextRecord();

    $xml = new SimpleXMLElement("<addon></addon>");
    $xml->addChild("name", $this->addon);
    $xml->addChild("author", "Wuild");
    $xml->addChild("system", "openTracker");
    $xml->addChild("minrevision", $db->system_revision);
    if (count($folders) > 0) {
        $xml_folders = $xml->addChild("folders");
        foreach ($folders as $folder) {
            $test = $xml_folders->addChild("folder");
            $test->addChild("name", $folder);
        }
    }

    if (count($folder_files) > 0) {
        $xml_files = $xml->addChild("files");
        foreach ($folder_files as $folder) {

            foreach ($folder['files'] as $file) {
                $path = PATH_APPLICATIONS . $this->addon . "/" . $folder['name'] . "/" . $file;
                $test = $xml_files->addChild("file");
                $name = explode(".", $file);
                $ext = end($name);
                $test->addChild("name", $name[0]);
                $test->addChild("ext", $ext);
                $test->addChild("size", filesize($path));
                $test->addChild("path", $this->addon . "/" . $folder['name'] . "/");
                $content = file_get_contents($path);
                $test->addChild("data", base64_encode($content));
            }
        }
    }

    if (count($files) > 0) {
        $xml_files = $xml->addChild("files");
        foreach ($files as $file) {
            $path = PATH_APPLICATIONS . $this->addon . "/" . $file;
            $test = $xml_files->addChild("file");
            $name = explode(".", $file);
            $ext = end($name);
            $test->addChild("name", $name[0]);
            $test->addChild("ext", $ext);
            $test->addChild("size", filesize($path));
            $test->addChild("path", $this->addon . "/");
            $content = file_get_contents($path);
            $test->addChild("content", base64_encode($content));
        }
    }

    $dom = dom_import_simplexml($xml)->ownerDocument;
    $dom->formatOutput = true;
    die($dom->saveXML());
} Catch (Exception $e) {
    echo $e->getMessage();
}
?>
