<?php

$this->setTitle("Admin - Settings");
$this->addJavascript("sortable.js");
$this->setSidebar(true);
try {
    $acl = new Acl(USER_ID);
    if (!$acl->Access("z"))
        throw new Exception("Access denied");

    $this->menu["Navigations"] = page("admin", "navigation");
    $this->menu["Create item"] = page("admin", "navigation", "create");
    
    $action = isset($this->args["var_a"]) ? $this->args['var_a'] : "";

    $tpl = new Template(PATH_APPLICATIONS . "admin/tpl/navigation/");
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
