<h4><?php echo _t("Create category") ?></h4>
<?php
if (isset($_POST['create'])) {
    try {

        if (empty($_POST['name']))
            throw new Exception("Missing category name");

        if (empty($_POST['icon']))
            throw new Exception("Missing category icon");
        
        $db = new DB("categories");
        $db->setColPrefix("category_");
        $db->name = $_POST['name'];
        $db->icon = $_POST['icon'];
        $db->insert();
        
        header("location: ".page("admin", "categories"));
        
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>
<form method="post">
    <table>
        <tr>
            <td><?php echo _t("Name") ?></td>
            <td><input type="text" size="20" name="name"></td>
        </tr>
        <tr>
            <td><?php echo _t("Icon") ?></td>
            <td><input type="text" size="20" name="icon"></td>
        </tr>
        <tr>
            <td><input type="submit" name="create" value="<?php echo _t("Create") ?>" /></td>
        </tr>
    </table>
</form>