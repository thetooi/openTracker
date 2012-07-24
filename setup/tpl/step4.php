<?php
try {

    if ($_SESSION['action'] != "step4")
        throw new Exception("Access denied");
    ?>
    <h4>Setup admin account</h4>
    <form method='post'>
        <table>
            <tr>
                <td>Username</td>
            </tr>
            <tr>
                <td><input type='text' name='username' size='30' /></td>
            </tr>
            <tr>
                <td>Email</td>
            </tr>
            <tr>
                <td><input type='text' name='email' size='30' /></td>
            </tr>
            <tr>
                <td>Password</td>
            </tr>
            <tr>
                <td><input type='password' name='password' size='30' /></td>
            </tr>
            <tr>
                <td>Confirm password</td>
            </tr>
            <tr>
                <td><input type='password' name='password2' size='30' /></td>
            </tr>
            <tr>
                <td><input type='submit' name='register' value='Continue'></td>
            </tr>
        </table>
    </form>
    <?php
    if (isset($_POST['register'])) {
        try {

            include(PATH_LIBRARY . "Main.php");
            include(PATH_LIBRARY . "DB.php");

            if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['password2']))
                throw new Exception("Missing information");

            $username = $_POST['username'];
            $password = $_POST['password'];
            $password2 = $_POST['password2'];
            $email = $_POST['email'];

            if (strlen($username) < 4)
                throw new Exception("Username is to short, minimum 4 characters");

            if (strlen($password) < 6)
                throw new Exception("Password is to short, minimum 6 characters");

            if (!validate_string($username))
                throw new Exception("Invalid characters in the username");

            if ($password != $password2)
                throw new Exception("Passwords did not match");

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                throw new Exception("Invalid email address");

            $db = new DB("users");
            $db->select("user_email = '" . $email . "'");
            if ($db->numRows())
                throw new Exception("Email is already registered");

            $db = new DB("users");
            $db->select("user_name = '" . $email . "'");
            if ($db->numRows())
                throw new Exception("Username does already exist");

            $id = uniqid(true);
            $passkey = md5(uniqid(true));
            $password_secret = generatePassword(12);
            $password_hash = md5($password_secret . $password . $password_secret);


            $db = new DB("users");
            $db->setColPrefix("user_");
            $db->id = $id;
            $db->name = $username;
            $db->email = $email;
            $db->password = $password_hash;
            $db->password_secret = $password_secret;
            $db->status = 4;
            $db->ip = $_SERVER['REMOTE_ADDR'];
            $db->last_login = time();
            $db->last_access = time();
            $db->passkey = $passkey;
            $db->group = 12;
            $db->added = time();
            $db->uploader = 1;
            $db->insert();
            //echo $db->affectedRows();
            $uid = $id;

            $db = new DB("news");
            $db->setColPrefix("news_");
            $db->added = time();
            $db->userid = $uid;
            $db->subject = "Welcome";
            $db->content = "to your openTracker website, to get started go to the admin panel and start messing around! :)";
            $db->insert();

            $db = new DB("forum_categories");
            $db->setColPrefix("category_");
            $db->title = "Forum";
            $db->sort = 0;
            $db->group = 1;
            $db->insert();
            $cat_id = $db->getId();
 
           $db = new DB("forum_forums");
            $db->setColPrefix("forum_");
            $db->name = "openTracker";
            $db->description = "This is a forum";
            $db->group = 1;
            $db->category = $cat_id;
            $db->sort = 0;
            $db->insert();
            $forum_id = $db->getId();

            $db = new DB("forum_topics");
            $db->setColPrefix("topic_");
            $db->userid = $uid;
            $db->subject = "Forum topic";
            $db->forum = $forum_id;
            $db->locked = 0;
            $db->sticky = 0;
            $db->insert();
            $topic_id = $db->getId();

            $db = new DB("forum_posts");
            $db->setColPrefix("post_");
            $db->topic = $topic_id;
            $db->user = $uid;
            $db->content = "This is a forum topic";
            $db->added = time();
            $db->edited_by = 0;
            $db->edited_date = 0;
            $db->insert();

            $_SESSION['action'] = "step5";
            header("location: ?action=step5");
        } Catch (Exception $e) {
            echo error($e->getMessage());
        }
    }
} Catch (Exception $e) {
    echo error($e->getMessage());
}
?>