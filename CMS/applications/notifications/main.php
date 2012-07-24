<?php

$noti = new notifications_main();
echo "<h4>Notifications</h4>";
echo "<ul style='list-style:none; padding: 0px;'>";
echo $noti->load("");
echo "</ul>";
?>