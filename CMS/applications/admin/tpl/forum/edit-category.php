<h4>Edit forum category</h4>

<?php
try {

    if (!isset($_GET['id']) || !intval($_GET['id']))
        throw new Exception("missing id");

    $id = $_GET['id'];

    if (isset($_POST['save'])) {
        try {
            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("invalid token");

            if (empty($_POST['title']))
                throw new Exception("Cannot create a category without a name");

            if (!intval($_POST['group']))
                throw new Exception("invalid form data");

            $db = new DB("forum_categories");
            $db->setColPrefix("category_");
            $db->title = $_POST['title'];
            $db->group = $_POST['group'];
            $db->update("category_id = '" . $db->escape($id) . "'");

            header("location: " . page("admin", "forum"));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }

    $db = new DB("forum_categories");
    $db->setColPrefix("category_");
    $db->select("category_id = '" . $db->escape($id) . "'");
    $db->nextRecord();
    ?>

    <form method="POST">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>" />
        <table>
            <tr>
                <td><?php echo _t("Category name") ?></td>
                <td><input type="text" name="title" value="<?php echo $db->title; ?>" size="40" /></td>
            </tr>
            <tr>
                <td><?php echo _t("Group") ?></td>
                <td><select name="group"><?php echo getGroups($db->group) ?></select></td>
            </tr>
            <tr>
                <td><input type="submit" value="Save category" name="<?php echo _t("save") ?>" /></td>
            </tr>
        </table>
    </form>

    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>