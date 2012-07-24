<h4>Installation</h4>
<?php
if ($_SESSION['action'] == "step3") {
    if (!isset($_POST['continue'])) {
        ?>
        <form method="post">
            By pressing continue the setup will start installing the database.<br />
            This may take a while.<br /><br />
            <input type="submit" name="continue" value="Continue">
        </form>
        <?php
    } else {
        if (installDB()) {
            $_SESSION['action'] = "step4";
            header("location: ?action=step4");
        }
    }
} else {
    echo error("Access denied");
}
?>