<?php
$this->setTitle("Quote Post");

try {

    $acl = new Acl(USER_ID);

    if (!$acl->Access("x"))
        throw new Exception("Access Denied");

    if (!isset($_GET['id']))
        throw new Exception("missing post");

    if (!intval($_GET['id']))
        throw new Exception("invalid id");


    $post = new DB("forum_posts");
    $post->join("left", "{PREFIX}forum_topics", "topic_id", "post_topic");
    $post->select("post_id = '" . $post->escape($_GET['id']) . "'");
    $post->nextRecord();

    if (isset($_GET['confirm'])) {
        ?>
        <div class="user" style="float:left; margin: 3px; border: 1px solid #ddd; padding:5px; padding-bottom: 10px; background-color: #f8f8f8; width: 47%;">
            <center>Are you sure you wish to delete this?<br /><br />
                <a href="<?php echo page("forums", "delete-post", "", "", "", "id=" . $_GET['id']) ?>"><span class="btn red">Yes</span></a> 
                <a href="<?php echo page("forums", "view-topic", $post->topic_subject . "-" . $post->topic_id) ?>"><span class="btn">No</span></center></a>
        </div>
        <?
    } else {
        $db = new DB("forum_posts");
        $db->delete("post_id = '" . $db->escape($_GET['id']) . "'");
        header("location: " . page("forums", "view-topic", $post->topic_subject . "-" . $post->topic_id));
    }
} Catch (Exception $e) {
    echo error($e->getMessage());
}
?>
