<?php

/**
 * @author Wuild
 */
class Template {
    public $path = "";
    private $files = array();
    public $data = "";
    private $vars = array();
    public $title = "";
    public $sidebar = false;
    public $javascript = array();
    public $css = array();

    /**
     * Set the path to the template
     * @param string $path
     */
    function __construct($path) {
        $this->path = $path;
    }

    /**
     * Load a template file
     * @param string $file template name with extension.
     */
    function loadFile($file) {
        $this->files[] = $file;
    }

    /**
     * Set a template variable that can be used by using the __get() function
     * @param string $name variable name
     * @param string $value variable value
     */
    function __set($name, $value) {
        $this->vars[$name] = $value;
    }

    /**
     * Get a template variable by using $template->variable or inside a template $this->variable
     * @param string $name 
     * @return string returns the value of the variable
     */
    function __get($name) {
        return $this->vars[$name];
    }

    /**
     * Set the title of the current application
     * @param string $title page title
     */
    function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Show the sidebar on the current application
     * @param boolean $sidebar default = false
     */
    function setSidebar($sidebar = false) {
        $this->sidebar = $sidebar;
    }

    /**
     * Add a custom javascript into the current application.
     * this will load the $script at the current application path
     * @param string $script javascript name
     */
    function addJavascript($script) {
        $this->javascript[] = $script;
    }

    /**
     * Add a custom stylesheet into the current application.
     * this will load the $script at the current application path
     * @param string $script stylesheet name
     */
    function addCss($script) {
        $this->css[] = $script;
    }

    /**
     * Build the selected template or loaded templates.
     * @param string file build file, leave empty if using loadFile function
     */
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

    /**
     * Build the loaded templates into a variable
     * @return string returns the templates as a variable
     */
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
