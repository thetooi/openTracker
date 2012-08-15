<?php

/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * filename library/Widget.php
 * 
 * @author Wuild
 * @package openTracker.Widget
 */
class Widget {

    /**
     * Widget path
     * @var string 
     */
    private $_path;

    /**
     * Construct the application widget
     * @param type $app 
     */
    function __construct($app = "") {
        if ($app != "")
            $this->_path = PATH_APPLICATIONS . $app . "/";
    }

    /**
     * Check if the application has a widget
     * @param string $app
     * @return boolean 
     */
    function hasWidget($app) {
        if (file_exists(PATH_APPLICATIONS . $app . "/widget.php"))
            return true;
        else
            return false;
    }

    /**
     * Build the selected widget.
     * @return string
     * @throws Exception 
     */
    function build() {
        try {
            if (!file_exists($this->_path . "widget.php"))
                throw new Exception("Widget not found");

            $widget = new Template($this->_path);
            $widget->loadFile("widget.php");

            $tpl = new Template(PATH_CMS . "templates/");
            $tpl->content = $widget->buildVar();
            $tpl->loadFile("widget.tpl.php");
            return $tpl->buildVar();
        } Catch (Exception $e) {
            echo _t($e->getMessage());
        }
    }

}

?>
