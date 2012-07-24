<?php

$this->setTitle("Rules");
$db = new DB("rules");
$db->setColPrefix("rule_");
$db->select("rule_lang = '" . CURRENT_LANGUAGE . "'");
if (!$db->numRows())
    $db->select("rule_lang = '" . DEFAULT_LANGUAGE . "'");
$db->nextRecord();
echo htmlformat($db->content, true);
?>