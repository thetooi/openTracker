<h4>mySQL query</h4>
Use {PREFIX} to get your current database prefix.
<?php
try {
    ?>
    <form method = "post">
        <input type = "hidden" name = "secure_input" value = "<?php echo $_SESSION['secure_token_last'] ?>">
        <textarea cols="70" name="query" rows="4"><?php echo isset($_POST['query']) ? $_POST['query'] : "SELECT * FROM {PREFIX}users" ?></textarea><br />
        <input type="submit" value="Query" />
    </form>
    <?php
    if (isset($_POST['query'])) {
        try {

            $query = $_POST['query'];

            if (empty($_POST['query']))
                throw new Exception("INVALID QUERY");

            $db = new DB;
            $db->query(stripslashes($db->escape($query)));

            echo notice($query . "<br />Affected rows " . $db->affectedRows());

        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>