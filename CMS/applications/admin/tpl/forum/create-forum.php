<?php

/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Wuild
 * @package openTracker
 */

if(!defined("INCLUDED"))
    die("Access denied");

?>

<h4>Create new forum</h4>

<?php
try {

    $db = new DB("forum_categories");
    $db->select();
    if(!$db->numRows())
        throw new Exception("No categories.");
    
    if (isset($_POST['create'])) {
        try {

            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("invalid token");

            if (empty($_POST['name']))
                throw new Exception("Cannot create a forum without a name");

            if (!intval($_POST['category']) || !intval($_POST['group']))
                throw new Exception("invalid form data");

            $db = new DB("forum_forums");
            $db->setColPrefix("forum_");
            $db->name = $_POST['name'];
            $db->description = $_POST['description'];
            $db->group = $_POST['group'];
            $db->category = $_POST['category'];
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
                <td>Forum name</td>
                <td><input type="text" name="name" size="40"></td>
            </tr>
            <tr>
                <td>Category</td>
                <td><select name="category"><?php echo getForumCategory() ?></select></td>
            </tr>
            <tr>
                <td>Group</td>
                <td><select name="group"><?php echo getGroups() ?></select></td>
            </tr>
            <tr>
                <td valign="top">Description</td>
                <td><textarea name="description" rows="5" cols="31"></textarea></td>
            </tr>
            <tr>
                <td><input type="submit" value="Create" name="create" /></td>
            </tr>
        </table>
    </form>

    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>