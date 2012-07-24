<?php

define("REVISION", "14");

$system = new DB("system");
$system->setColPrefix("system_");
$system->select();
if (!$system->numRows()) {
    $system->revision = 0;
    $system->insert();
}
$system->nextRecord();
$rev = $system->revision;
$query = array();

if ($rev < 14) {
    $query[] = "ALTER TABLE  `{PREFIX}users` ADD  `user_invited` INT NOT NULL";
}



if ($system->revision < REVISION) {
    $system->revision = REVISION;
    $system->update();
}

$db = new DB;
if (count($query)) {
    foreach ($query as $sql) {
        $db->query($sql);
    }
}
?>