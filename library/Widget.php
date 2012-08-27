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
    public $css = array();

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

    function loadCss($script) {
        $path = str_replace(PATH_ROOT, "", $this->_path."css/");
        $this->css[] = $path . $script;
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
            $tpl->test = $this;
            $tpl->content = $widget->buildVar();
            $data = $tpl->buildVar("widget.tpl.php");
            foreach ($widget->css as $css) {
                $this->loadCss($css);
            }
            return $data;
        } Catch (Exception $e) {
            echo _t($e->getMessage());
        }
    }

}

?>
