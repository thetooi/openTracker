<?php
$wpref = new Pref("website");
?>
<div id="toolbar">
    <div id="inner">
        <a href="<?php echo CMS_URL; ?>" class="brand"><?php echo $wpref->name; ?></a>
        <ul id="menu">
            <li class="item"><a href="#" class="menu" rel="admin_tools">Admin Tools</a>
                <ul class="dropdown" id="admin_tools">
                    <li class="title"><a>Administration Tools</a></li>
                    <li>
                        <a href="<?php echo page("admin", "settings"); ?>">
                            <i class="icon"><img src="images/admin/settings.png" width="16px"></i>&nbsp;Settings
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo page("admin", "news"); ?>">
                            <i class="icon"><img src="images/admin/news.png" width="16px"></i>&nbsp;News
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo page("admin", "forum"); ?>">
                            <i class="icon"><img src="images/admin/forum.png" width="16px"></i>&nbsp;Forum
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo page("admin", "navigation"); ?>">
                            <i class="icon"><img src="images/admin/navigation.png" width="16px"></i>&nbsp;Navigation
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo page("admin", "groups"); ?>">
                            <i class="icon"><img src="images/admin/groups.png" width="16px"></i>&nbsp;Groups
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo page("admin", "addons"); ?>">
                            <i class="icon"><img src="images/admin/addons.png" width="16px"></i>&nbsp;Addons
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo page("admin", "documents"); ?>">
                            <i class="icon"><img src="images/admin/documents.png" width="16px"></i>&nbsp;Documents
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo page("admin", "widgets"); ?>">
                            <i class="icon"><img src="images/admin/widgets.png" width="16px"></i>&nbsp;Widgets
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo page("admin", "support"); ?>">
                            <i class="icon"><img src="images/admin/support.png" width="16px"></i>&nbsp;Support
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <ul id="icons">
        </ul>
    </div>
</div>