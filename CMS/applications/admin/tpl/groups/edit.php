<?php
try {
    if (!isset($_GET['id']))
        throw new Exception("Missing news id");

    if (!intval($_GET['id']))
        throw new Exception("Invalid id");

    $id = 0 + $_GET['id'];


    if (isset($_POST['save'])) {
        try {

            if ($_POST['secure_input'] != $_SESSION['secure_token'])
                throw new Exception("Wrong secure token");

            if (empty($_POST['name']) || empty($_POST['name']))
                throw new Exception("Missing data");

            $db = new DB("groups");
            $db->setColPrefix("group_");
            $db->id = $_POST['id'];
            $db->name = $_POST['name'];
            $db->acl = $_POST['acl'];
            if (isset($_POST['upgradable'])) {
                if ($_POST['upgradeto'] == 0)
                    throw new Exception("Missing upgrade to group data");
                $db->upgradable = 1;
                $db->upgradeto = $_POST['upgradeto'];
                $db->downgradeto = $_POST['downgradeto'];
                $db->minupload = $_POST['minupload'];
                $db->minratio = $_POST['minratio'];
            }
            $db->update("group_id = '" . $id . "'");
            header("location: ".page("admin", "groups"));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }

    $db = new DB("groups");
    $db->setColPrefix("group_");
    $db->select("group_id = '" . $db->escape($id) . "'");
    if (!$db->numRows())
        throw new Exception("Group not found");

    $db->nextRecord();
    ?>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#upgrade_check").change(function(){
                if($(this).is(":checked")){
                    $("#upgradable").show();
                }else{
                    $("#upgradable").hide();
                }
            });
        });
    </script>
    <h4>Create group</h4>
    <form method="post">
        <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
        <table>
            <tr>
                <td width="100px"><?php echo _t("Group Id")?>:</td>
                <td><input name="id" type="text" value="<?php echo $db->id ?>" size="25"></td>
            </tr>
            <tr>
                <td width="100px"><?php echo _t("Group name")?>:</td>
                <td><input name="name" type="text" value="<?php echo $db->name ?>" size="25"></td>
            </tr>
            <tr>
                <td><?php echo _t("Group ACL")?>:</td>
                <td><input name="acl" type="text" value="<?php echo $db->acl ?>" size="25"></td>
            </tr>

            <tr>
                <td><?php echo _t("Upgradable")?>:</td>
                <td><input name="upgradable" id="upgrade_check" <?php echo ($db->upgradable == 1) ? "CHECKED" : "" ?> type="checkbox"></td>
            </tr>
        </table>
        <table id="upgradable" style="display:<?php echo ($db->upgradable == 1) ? "block" : "none" ?>;">
            <tr>
                <td width="100px"><?php echo _t("Upgrade to")?>:</td>
                <td><select name="upgradeto"><option value="0">(<?php echo _t("Choose")?>)</option><?php echo getGroups($db->upgradeto); ?></select></td>
            </tr>
            <tr>
                <td width="100px"><?php echo _t("Downgrade to")?>:</td>
                <td><select name="downgradeto"><option value="0">(<?php echo _t("Choose")?>)</option><?php echo getGroups($db->downgradeto); ?></select></td>
            </tr>
            <tr>
                <td><?php echo _t("Minimum Upload")?>:</td>
                <td><input name="minupload" type="text" value="<?php echo $db->minupload ?>" size="25"></td>
            </tr>
            <tr>
                <td><?php echo _t("Minimum Ratio")?>:</td>
                <td><input name="minratio" value="<?php echo $db->minratio ?>" type="text"></td>
            </tr>
        </table>
        <input type="submit" name="save" value="<?php echo _t("Save group")?>" />
    </form>

    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>