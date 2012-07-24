<h4>Install widget</h4>

<?php
$apps = makefilelist(PATH_APPLICATIONS, ".|..", true, "folders");

$widgets = array();

$widget = new Widget;
foreach($apps as $app){
    if($widget->hasWidget($app))
        $widgets[] = $app;
}

?>
<form method="post">
    <table>
        <tr>
            <td><?php echo _t("Name"); ?></td>
            <td><input type="text" name="name" /></td>
        </tr>
        <tr>
            <td><?php echo _t("Widget"); ?></td>
            <td><select name="module"><?php echo makefileopts($widgets); ?></select></td>
        </tr>
        <tr>
            <td><?php echo _t("Visible for"); ?></td>
            <td><select name="group"><?php echo getGroups(); ?></select> <?php echo _t("and above"); ?></td>
        </tr>
        <tr>
            <td><input type="submit" name="install" value="<?php echo _t("Install"); ?>"></td>
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
        $db->insert();

        header("location: " . page("admin", "widgets"));
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>