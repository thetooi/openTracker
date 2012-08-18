<?php

try {

    include(PATH_LIBRARY . "Main.php");
    include(PATH_LIBRARY . "Pref.php");
    include(PATH_LIBRARY . "DB.php");

    $pref = new Pref("website");
    
    if ($_SESSION['action'] != "step6")
        throw new Exception("Access denied");
    ?>
    <h4>Success!!!</h4>
    Your copy of openTracker has been installed.<br />
    We recommend that you delete the setup folder.
    <br /><br /><br />
    <a href="<?php echo $pref->url ?>"><span class="btn blue big">CONTINUE</a></a><br /><br />

    <?php
} Catch (Exception $e) {
    echo error($e->getMessage());
}
?>