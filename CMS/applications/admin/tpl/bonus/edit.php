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
if (!defined("INCLUDED"))
    die("Access denied");

try {

    if (!$this->id)
        throw new Exception("Missing id");

    $db = new DB("bonus");
    $db->select("bonus_id = '" . $db->escape($this->id) . "'");

    if (!$db->numRows())
        throw new Exception("Invalid id");

    $types = array(
        1 => "Remove Download",
        2 => "Add Upload",
        3 => "Add Invite slot",
    );

    if (isset($_POST['add'])) {
        try {
            if (!isset($_POST['id']))
                throw new Exception("Invalid id");

            $id = $_POST['id'];

            $db = new DB("bonus");
            $db->select("bonus_id = '" . $db->escape($id) . "'");

            if (!$db->numRows())
                throw new Exception("Invalid id");

            if (!isset($_POST['title']) || empty($_POST['title']))
                throw new Exception("missing data");

            if (!isset($_POST['description']) || empty($_POST['description']))
                throw new Exception("missing data");

            if (!isset($_POST['data']) || empty($_POST['data']))
                throw new Exception("missing data");

            if (!intval($_POST['type']))
                throw new Exception("Invalid type");

            if (!intval($_POST['cost']))
                throw new Exception("Is not numeric");

            $db = new DB("bonus");
            $db->setColPrefix("bonus_");
            $db->title = $_POST['title'];
            $db->description = $_POST['description'];
            $db->type = $_POST['type'];
            $db->data = $_POST['data'];
            $db->cost = $_POST['cost'];
            $db->update("bonus_id = '" . $db->escape($id) . "'");
            header("location: " . page("admin", "bonus"));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }

    $db = new DB("bonus");
    $db->setColPrefix("bonus_");
    $db->select("bonus_id = '" . $db->escape($this->id) . "'");

    if (!$db->numRows())
        throw new Exception("Invalid id");
    $db->nextRecord();
    ?>
    <form method="post">
        <input type="hidden" name="id" value="<?php echo $db->id; ?>">
        <table>
            <tr>
                <td width="120px">Title</td>
                <td><input type="text" name="title" value="<?php echo $db->title; ?>" size="30"></td>
            </tr>
            <tr>
                <td width="120px">Description</td>
                <td><?php echo bbeditor("description", 10, 85, $db->description); ?></td>
            </tr>
            <tr>
                <td width="120px">Type</td>
                <td>
                    <select name="type">
                        <?php
                        foreach ($types as $id => $title) {
                            echo "<option value='$id' " . ($db->type == $id ? "SELECTED" : "") . ">" . _t($title) . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="120px">Costs</td>
                <td><input type="text" name="cost" value="<?php echo $db->cost; ?>" size="20"> Bonus points</td>
            </tr>
            <tr>
                <td width="120px">Amount</td>
                <td><input type="text" name="data" value="<?php echo $db->data ?>" size="20"></td>
            </tr>
            <tr>
                <td width="120px"><input type="submit" value="<?php echo _t("Save item") ?>" name="add"></td>
                <td></td>
            </tr>
        </table>
    </form>

    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
