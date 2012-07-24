<?php

setcookie(COOKIE_PREFIX . "user", "", time() - 7200, "/", "", "0");
setcookie(COOKIE_PREFIX . "lastvisit", "", time() - 7200, "/", "", "0");

header("location: " . page(START_APP));
?>
