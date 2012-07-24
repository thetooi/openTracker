<h4>Database configs</h4>

<form method="post" style="float:left; width: 100%;">
    <table>
        <tr>
            <td><b>Mysql Library:</b></td>
            <td><select name="type"><option value="mysqli">Mysqli</option><option value="mysql">Mysql</option></select></td>
        </tr>
        <tr>
            <td><b>Hostname:</b></td>
            <td><input type="text" name="hostname" value="<?php echo (isset($_POST['hostname']) ? $_POST['hostname'] : "localhost") ?>"></td>
        </tr>
        <tr>
            <td><b>Username:</b></td>
            <td><input type="text" name="username" value="<?php echo (isset($_POST['username']) ? $_POST['username'] : "root") ?>"></td>
        </tr>
        <tr>
            <td><b>Password:</b></td>
            <td><input type="password" name="password" value="<?php echo (isset($_POST['password']) ? $_POST['password'] : "") ?>"></td>
        </tr>
        <tr>
            <td><b>Database:</b></td>
            <td><input type="text" name="database" value="<?php echo (isset($_POST['database']) ? $_POST['database'] : "") ?>"></td>
        </tr>
        <tr>
            <td><b>Table Prefix:</b></td>
            <td><input type="text" name="prefix" value="<?php echo (isset($_POST['prefix']) ? $_POST['prefix'] : "tracker_") ?>"></td>
        </tr>
        <tr>
            <td><input type="submit" name="check" value="Check information" /></td>
        </tr>
    </table>
</form>

<?php
if (isset($_POST['check'])) {
    try {
        switch ($_POST['type']) {
            case 'mysql':
                $mysql = @mysql_connect($_POST['hostname'], $_POST['username'], $_POST['password']);
                if (!$mysql)
                    throw new Exception(mysql_error());
                $db = @mysql_select_db($_POST['database']);
                if (!$db)
                    throw new Exception(mysql_error());
                break;
            case 'mysqli':
                $mysql = @mysqli_connect($_POST['hostname'], $_POST['username'], $_POST['password']);
                if (!$mysql)
                    throw new Exception(mysqli_connect_error($mysql));
                $db = @mysqli_select_db($mysql, $_POST['database']);
                if (!$db)
                    throw new Exception(mysqli_error($mysql));
                break;
        }
        $body = "<?php\n\n";
        $body .= "$" . "config = array('mysql');\n\n";
        $body .= "$" . "config['mysql']['hostname'] = '" . $_POST['hostname'] . "';\n";
        $body .= "$" . "config['mysql']['username'] = '" . $_POST['username'] . "';\n";
        $body .= "$" . "config['mysql']['password'] = '" . $_POST['password'] . "';\n";
        $body .= "$" . "config['mysql']['database'] = '" . $_POST['database'] . "';\n";
        $body .= "$" . "config['mysql']['prefix'] = '" . $_POST['prefix'] . "';\n";
        $body .= "$" . "config['mysql']['type'] = '" . $_POST['type'] . "';\n\n";
        $body .= "?>";

        if ($mysql) {
            if (file_exists(PATH_CONFIGS . "database.php"))
                @unlink(PATH_CONFIGS . "database.php");
            $database = PATH_CONFIGS . "database.php";
            $file = fopen($database, 'w') or die("can't open file");
            fclose($file);
            file_put_contents(PATH_CONFIGS . "database.php", $body);
        }
        $_SESSION['action'] = "step3";

        echo "<font color='green'>Database check passed.</font><br /><br /> <a href='?action=step3'><span class='btn blue big'>Continue</span></a><br /><br />";
    } Catch (Exception $e) {
        echo error($e->getMessage());
    }
}
?>