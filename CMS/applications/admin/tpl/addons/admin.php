<?php

$app = new Addon($this->addon);

try {

    if (!$app->hasAdmin())
        throw new Exception("no admin found");

    include(PATH_APPLICATIONS . $this->addon . "/admin/main.php");
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
