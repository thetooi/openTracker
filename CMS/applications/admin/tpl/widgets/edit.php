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
?>
<h4>Edit widget</h4>

<?php
try {

    if (!intval($this->id))
        throw new Exception("invalid id");

    if ($this->id == false)
        throw new Exception("invalid id");

    $apps = makefilelist(PATH_APPLICATIONS, ".|..", true, "folders");

    $widgets = array();

    $widget = new Widget;
    foreach ($apps as $app) {
        if ($widget->hasWidget($app))
            $widgets[] = $app;
    }

    $db = new DB("widgets");
    $db->select("widget_id = '" . $db->escape($this->id) . "'");
    if (!$db->numRows())
        throw new Exception("Could not find widget.");
    $db->nextRecord();
    ?>
    <form method="post">
        <table>
            <tr>
                <td><?php echo _t("Name"); ?></td>
                <td><input type="text" name="name" value="<?php echo $db->widget_name; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo _t("Widget"); ?></td>
                <td><select name="module"><?php echo makefileopts($widgets, $db->widget_module); ?></select></td>
            </tr>
            <tr>
                <td><?php echo _t("Visible for"); ?></td>
                <td><select name="group"><?php echo getGroups($db->widget_group); ?></select> <?php echo _t("and above"); ?></td>
            </tr>
            <tr>
                <td><input type="submit" name="install" value="<?php echo _t("Save"); ?>"></td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['install'])) {
        try {

            if (empty($_POST['name']))
                throw new Exception("missing form");

            $db = new DB("widgets");
            $db->setColPrefix("widget_");
            $db->name = $_POST['name'];
            $db->module = $_POST['module'];
            $db->group = $_POST['group'];
            $db->update("widget_id = '" . $db->escape($this->id) . "'");

            header("location: " . page("admin", "widgets"));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>