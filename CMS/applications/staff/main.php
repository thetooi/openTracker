<h4><?php echo _t("Staff Members") ?></h4>
<?php
$this->setTitle("Staff Members");

$db = new DB("users");
$db->setColPrefix("user_");
$db->select("user_group >= '10'");
while ($db->nextRecord()) {
    $acl = new Acl($db->id);
    $time = time() - 200;
    $online = ($acl->last_access < $time) ? "Last seen: " . get_date($acl->last_access) : "<b><font color='green'>Online</font></b>";
    ?>
    <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
        <img width="50px" src="<?php echo $acl->avatar(); ?>" style="float:left; margin-right: 5px;" alt="">
        <a href="<?php echo page("profile", "view", $acl->name) ?>"><b><?php echo $acl->name ?></b></a> (<?php echo $acl->group_name ?>)<br /><?php echo $online; ?>
        <br /><br />
        <a href="<?php echo page("profile", "mailbox", "view", "", "", "uid=" . $acl->id) ?>"><span class="btn">PM</span></a> 
        <a href="<?php echo page("profile", "view", $acl->name) ?>"><span class="btn">Profile</span></a>
    </div>
<?php } ?>
