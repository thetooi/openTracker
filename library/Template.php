<?php

class Template{

    public $path = "";
    private $files = array();
    public $data = "";
    private $vars = array();
    public $title = "";
    public $sidebar = false;
    public $javascript = array();
    public $css = array();

    function __construct($path) {
        $this->path = $path;
    }

    function loadFile($file) {
        $this->files[] = $file;
    }

    function __set($name, $value) {
        $this->vars[$name] = $value;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setSidebar($sidebar = false) {
        $this->sidebar = $sidebar;
    }

    function addJavascript($script) {
        $this->javascript[] = $script;
    }

    function addCss($script) {
        $this->css[] = $script;
    }

    function __get($name) {
        return $this->vars[$name];
    }

    function build($file = "") {
        if (count($this->files) > 0) {
            foreach ($this->files as $file) {
                if (file_exists($this->path . $file))
                    include($this->path . $file);
                else
                    echo "Template file not found";
            }
        }else {
            if (file_exists($this->path . $file))
                include($this->path . $file);
            else
                echo "Template file " . $this->path . $file . " not found";
        }
    }

    function buildVar() {
        global $page_title;
        foreach ($this->files as $file) {
            ob_start();
            if (file_exists($this->path . $file))
                include($this->path . $file);
            else
                echo "Error 404: file not found";

            $this->data .= ob_get_contents();
            ob_end_clean();
        }

        return $this->data;
    }

}

?>
