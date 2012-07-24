<?php

$this->setTitle("Admin - Settings");
$this->setSidebar(true);
try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("z"))
        throw new Exception("Access denied");

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/settings/");
    $tpl->loadFile("main.php");
    $tpl->build();
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
