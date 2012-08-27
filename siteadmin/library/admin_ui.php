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
class admin_ui {

    private $_menu_items = array();

    function addMenuItem($title, $link, $icon = "new_16.png") {
        $this->_menu_items[] = array($title, $link, $icon);
    }

    function build() {
        $html = "<ul class='iconButtons' style='display: block; margin-top: 5px; width: 100%; float:left; '>";
        foreach ($this->_menu_items as $item) {
            $html .= "<li><img src='siteadmin/images/icons/" . $item[2] . "' alt='' title=''><a href='" . $item[1] . "'>" . $item[0] . "</a></li>";
        }
        $html .= "</ul>";

        echo $html;
    }

}

?>
