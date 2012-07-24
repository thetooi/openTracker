<?php
$wpref = new Pref("website");
?>
<html>
    <body>
        <img src="<?php echo $wpref->url ?>images/logo.png"><br />
        <?php echo $this->content; ?>
    </body>
</html>