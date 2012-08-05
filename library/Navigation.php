<?php

/**
 * @author Wuild
 */
class Navigation {

    private $app;
    private $mod;

    function __construct($app = "", $mod = "") {
        $this->app = $app;
        $this->mod = $mod;
    }

    function item($title, $app, $mod = "") {
        if ($mod == "") {
            return "<li " . ($app == $this->app ? "class='selected'" : "") . "><a href='" . page($app) . "'>$title</a></li>";
        } else {
            return "<li " . ($app == $this->app && $mod == $this->mod ? "class='selected'" : "") . "><a href='" . page($app, $mod) . "'>$title</a></li>";
        }
    }

    function build() {
        $db = new DB("navigations");
        $db->setColPrefix("navigation_");
        $db->setSort("navigation_sorting ASC");
        $db->select("navigation_lang = '" . CURRENT_LANGUAGE . "'");
        if (!$db->numRows())
            $db->select("navigation_lang = '" . DEFAULT_LANGUAGE . "'");
        $menu = "";
        while ($db->nextRecord()) {
            if ($db->module != "")
                $menu .= $this->item($db->title, $db->application, $db->module);
            else
                $menu .= $this->item($db->title, $db->application);
        }

        return $menu;
    }

}

?>
