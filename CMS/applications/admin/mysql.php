<?php

$this->setTitle("Admin - mySQL");

try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("z"))
        throw new Exception("Access denied");

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/mysql/");
    $tpl->loadFile("query.php");
    $tpl->build();
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
