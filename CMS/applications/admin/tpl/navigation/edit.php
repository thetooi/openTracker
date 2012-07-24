<h4><?php echo _t("Edit navigation item") ?></h4>
<?php
try {


    if (!isset($_GET['id']))
        throw new Exception("Missing id");

    if (!intval($_GET['id']))
        throw new Exception("Invalid id");

    $id = 0 + $_GET['id'];

    $actions = array();

    $applications = makefilelist(PATH_APPLICATIONS, ".|..", true, "folders");

    foreach ($applications as $name) {
        $files = makefilelist(PATH_APPLICATIONS . $name, ".|..|ajax.php|tpl|index.html|install.php|widget.php", true);
        $actions[$name][] = $files;
    }


    if (isset($_POST['save'])) {
        try {
            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("Wrong secured token");

            if (empty($_POST['title']))
                throw new Exception("Empty title");

            $app = $_POST['application'];
            $action = $_POST['actions_' . $app] != "main" ? $_POST['actions_' . $app] : "";

            $db = new DB("navigations");
            $db->setColPrefix("navigation_");
            $db->title = $_POST['title'];
            $db->application = $app;
            $db->module = $action;
            $db->lang = $_POST['language'];
            $db->update("navigation_id = '" . $db->escape($id) . "'");

            header("location: " . page("admin", "navigation"));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }

    $db = new DB("navigations");
    $db->setColPrefix("navigation_");
    $db->select("navigation_id = '" . $db->escape($id) . "'");
    if (!$db->numRows())
        throw new Exception("Could not find item");

    $db->nextRecord();

    $module = $db->module == "" ? "main" : $db->module;
    ?>

    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <table>
            <tr>
                <td><?php echo _t("Title") ?></td>
                <td><input type="text" value="<?php echo $db->title; ?>" name="title"></td>
            </tr>
            <tr>
                <td><?php echo _t("Application") ?></td>
                <td><select id="application" name="application"><?php echo makefileopts($applications, $db->application); ?></select></td>
            </tr>
            <tr>
                <td><?php echo _t("Action module") ?></td>
                <td>
                    <?php
                    foreach ($actions as $name => $files) {
                        echo "<select id='actions_$name' name='actions_$name' class='nav_action' style='display:none;'>" . str_replace(".php", "", makefileopts($files[0], $module)) . "</select>";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td><?php echo _t("Language") ?></td>
                <td>
                    <select name="language"><?php echo getLanguages($db->lang) ?></select>
                </td>
            </tr>
            <tr>
                <td><input type="submit" name="save" value="<?php echo _t("Save item") ?>" /></td>
            </tr>
        </table>
    </form>

    <script type="text/javascript">
        $(document).ready(function(){
            var application = $("#application").val();
            $("#actions_"+application).val("<?php echo $module; ?>");
            $("#actions_"+application).show();
            $("#application").change(function(){
                var application = $(this).val();
                $(".nav_action").hide();
                $("#actions_"+application).show();
            });
        });
    </script>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>