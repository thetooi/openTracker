<h4>Create forum category</h4>

<?php
if (isset($_POST['create'])) {
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
        $db->insert();

        header("location: " . page("admin", "forum"));
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>

<form method="POST">
    <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>" />
    <table>
        <tr>
            <td>Category name</td>
            <td><input type="text" name="title" size="40" /></td>
        </tr>
        <tr>
            <td>Group</td>
            <td><select name="group"><?php echo getGroups() ?></select></td>
        </tr>
        <tr>
            <td><input type="submit" value="Create category" name="create" /></td>
        </tr>
    </table>
</form>