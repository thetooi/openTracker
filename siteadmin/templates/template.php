<?php
$acl = new Acl(USER_ID);
$wpref = new Pref("website");
$control = new Template(PATH_APPLICATIONS . $this->data['url']['application'] . "/");
$control->loadFile($this->data['url']['action'] . ".php");
$control->args = $this->data;
$content = $control->buildVar();
$title = ($control->title != "") ? " - " . $control->title : "";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <base href="<?php echo CMS_URL; ?>" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title><?php echo $wpref->name . ($title); ?></title>
        <link rel="stylesheet" href="css/site_root.css" />
        <link rel="stylesheet" href="css/impromptu.css" />
        <link href="siteadmin/css/structure.css" rel="stylesheet" type="text/css" />
        <script src='javascript/javascript.js.php?app=<?php echo $this->data['url']['application']; ?>' type='text/javascript' ></script>
        <script src='javascript/jquery-1.7.2.min.js' type='text/javascript' ></script>
        <script src='javascript/jquery-ui-1.8.21.custom.min.js' type='text/javascript' ></script>
        <script src='javascript/global.js' type='text/javascript' ></script>
        <script src='javascript/jquery-impromptu.js' type='text/javascript' ></script>
        <script src='javascript/jquery-impromptu-ext.js' type='text/javascript' ></script>
        <?php
        if ($acl->Access("x")) {
            ?>
            <link rel="stylesheet" href="siteadmin/css/toolbar.css" />
            <?php
        }
        if ($acl->Access("x")) {
            ?>
            <script src='siteadmin/javascript/toolbar.js' type='text/javascript'></script>
            <?php
        }

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
    <body class="full">
        <?php
        if ($acl->Access("x")) {
            $toolbar = new Template(PATH_SITEADMIN . "templates/");
            $toolbar->build("toolbar.php");
        }
        ?>
        <div id="wrapper">
            <div id="full_body_head">
                <div id="full_body_head_left">
                </div>		
                <div id="full_body_head_right">	
                </div>
            </div>
            <div id="full_content">
                <div id="admin_menu_main">
                    <ul id="menu">
                    </ul>
                </div>
                <div id="full_body">
                    <div class="content">
                        <?php echo $content; ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>