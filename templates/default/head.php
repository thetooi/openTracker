<link rel="stylesheet" href="templates/default/css/style.css" />
<?php
if ($this->sidebar) {
    ?>
    <link rel="stylesheet" href="templates/default/css/sidebar.css" />
    <?php
} else if ($this->login) {
    ?>
    <link rel="stylesheet" href="templates/default/css/login.css" />
    <?php
}
?>
<script src='javascript/jquery-bbedit.js' type='text/javascript' ></script>
<script src='javascript/jquery-tipsy.js' type='text/javascript' ></script>
<script src='javascript/custom.js' type='text/javascript' ></script>