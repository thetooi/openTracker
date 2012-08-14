<?php

$this->setTitle("Admin - Groups");
$this->setSidebar(true);
try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("z"))
        throw new Exception("Access denied");

    $action = isset($this->args["var_a"]) ? $this->args['var_a'] : "";

    $this->menu["Groups"] = page("admin", "groups");
    $this->menu["Create group"] = page("admin", "groups", "create");

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/groups/");
    switch ($action) {
        default:
            $tpl->loadFile("main.php");
            break;

        case 'edit':
            $tpl->groupid = (isset($this->args['var_b']) && getID($this->args['var_b'])) ? getID($this->args['var_b']) : 0;
            $tpl->loadFile("edit.php");
            break;

        case 'create':
            $tpl->loadFile("create.php");
            break;

        case 'delete':
            $tpl->loadFile("delete.php");
            break;
    }
    $tpl->build();
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
