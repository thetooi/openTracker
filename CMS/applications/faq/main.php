<?php

$this->setTitle("FAQ");

$db = new DB("faqs");
$db->setColPrefix("faq_");
$db->select("faq_lang = '" . CURRENT_LANGUAGE . "'");
if (!$db->numRows())
    $db->select("faq_lang = '" . DEFAULT_LANGUAGE . "'");
$db->nextRecord();
echo htmlformat($db->content, true);
?>