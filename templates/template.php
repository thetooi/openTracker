<?php
$acl = new Acl(USER_ID);
$wpref = new Pref("website");
$spref = new Pref("system");
$control = new Template(PATH_APPLICATIONS . $this->data['url']['application'] . "/");
$control->loadFile($this->data['url']['action'] . ".php");
$control->args = $this->data;
$tpl = new Template(PATH_TEMPLATES . $spref->template . "/");
$tpl->data = $this->data;
$tpl->sub_content = buildWidgets();
$tpl->content = $control->buildVar();
$tpl->sidebar = $control->sidebar;
$tpl->login = $this->login;
$title = ($control->title != "") ? " - " . $control->title : "";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <base href="<?php echo CMS_URL; ?>" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title><?php echo $wpref->name . ($title); ?></title>
        <link rel="stylesheet" href="css/site_root.css" />
        <link rel="stylesheet" href="css/impromptu.css" />
        <?php
        if ($acl->Access("x")) {
            ?>
            <link rel="stylesheet" href="siteadmin/css/toolbar.css" />
            <?php
        }
        ?>
        <script src='javascript/javascript.js.php?app=<?php echo $this->data['url']['application']; ?>' type='text/javascript' ></script>
        <script src='javascript/jquery-1.7.2.min.js' type='text/javascript' ></script>
        <script src='javascript/jquery-ui-1.8.21.custom.min.js' type='text/javascript' ></script>
        <script src='javascript/global.js' type='text/javascript' ></script>
        <script src='javascript/jquery-impromptu.js' type='text/javascript' ></script>
        <script src='javascript/jquery-impromptu-ext.js' type='text/javascript' ></script>
        <?php
        if ($acl->Access("x")) {
            ?>
            <script src='siteadmin/javascript/toolbar.js' type='text/javascript'></script>
            <?php
        }
        ?>
        <?php
        $tpl->build("head.php");

        if (count($control->javascript) > 0) {
            foreach ($control->javascript as $javascript) {
                ?>
                <script src='CMS/applications/<?php echo $this->data['url']['application'] . "/javascript/" . $javascript; ?>' type='text/javascript' ></script>
                <?
            }
        }

        if (count($control->css) > 0) {
            foreach ($control->css as $javascript) {
                ?>
                <link rel="stylesheet" href="templates/<?php echo $spref->template; ?>/css/<?php echo $javascript; ?>" />
                <?
            }
        }
        ?>
    </head>
    <body>
        <?php
        if ($acl->Access("x")) {
            $toolbar = new Template(PATH_SITEADMIN . "templates/");
            $toolbar->build("toolbar.php");
        }
        $tpl->build("template.php");
        ?>
        <div id="poweredBy">
            Powered by <a href="http://opentracker.nu">openTracker</a> <?php echo SYSTEM_VERSION; ?>
        </div>
    </body>
</html>