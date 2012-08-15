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
            <td width="100px"><?php echo _t("Group name") ?>:</td>
            <td><input name="name" type="text" size="25"></td>
        </tr>
        <tr>
            <td><?php echo _t("Group ACL") ?>:</td>
            <td><input name="acl" type="text" size="25"></td>
        </tr>

        <tr>
            <td><?php echo _t("Upgradable") ?>:</td>
            <td><input name = "upgradable" id = "upgrade_check" type = "checkbox"></td>
        </tr>
    </table>
    <table id = "upgradable" style = "display:none;">
        <tr>
            <td width = "100px"><?php echo _t("Upgrade to") ?>:</td>
            <td><select name="upgradeto"><option value="0">(Choose)</option><?php echo getGroups(); ?></select></td>
        </tr>
        <tr>
            <td width="100px"><?php echo _t("Downgrade to") ?>:</td>
            <td><select name = "downgradeto"><option value = "0">(Choose)</option><?php echo getGroups(); ?></select></td>
        </tr>
        <tr>
            <td><?php echo _t("Minimum Upload") ?>:</td>
            <td><input name="minupload" type="text" size="25"><br />(This data counts i kb)</td>
        </tr>
        <tr>
            <td><?php echo _t("Minimum Ratio") ?>:</td>
            <td><input name = "minratio" type = "text"></td>
        </tr>
    </table>
    <input type = "submit" name = "create" value = "<?php echo _t("Create group") ?>" />
</form>

<?php
if (isset($_POST['create'])) {
    try {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secure token");

        if (empty($_POST['name']) || empty($_POST['name']))
            throw new Exception("Missing data");

        $db = new DB("groups");
        $db->setColPrefix("group_");
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
        $db->insert();
        header("location: " . page("admin", "groups"));
    } Catch (Exception $e) {
        echo error(_t($e->getMessage()));
    }
}
?>