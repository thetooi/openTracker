<?php

/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Wuild
 * @package openTracker
 */

if(!defined("INCLUDED"))
    die("Access denied");

$this->setTitle("News");
$this->setSidebar(true);

$db = new DB("news");
$db->setColPrefix("news_");
$db->setSort("news_added DESC");
$db->select();
while ($db->nextRecord()) {
    $acl = new Acl($db->userid);
    ?>
    <div class="news">
        <h4><?php echo htmlformat($db->subject) ?></h4>
        <small><?php echo get_date($db->added, "", 1) ?></small>
        <p><?php echo htmlformat($db->content, true); ?></p>
    </div>
<?php } ?>
