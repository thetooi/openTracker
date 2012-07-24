<?php

class Main {

    private $main_template = "default";
    public $data = array("url" => array("application" => "", "action" => "main", "var_a" => "", "var_b" => "", "var_c" => ""));
    public $configs = array();
    private $control;
    private $startup;

    function __construct($startup = "") {

        $this->startup = $startup;

        $this->data['url']['application'] = (!isset($_GET['application']) || empty($_GET['application'])) ? $this->startup : $_GET['application'];
        $this->data['url']['application'] = (isset($_GET['application']) && !empty($_GET['application'])) ? $_GET['application'] : $this->data['url']['application'];
        $this->data['url']['action'] = (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] != "install") ? $_GET['action'] : $this->data['url']['action'];
        $this->data['url']['var_a'] = (isset($_GET['var_a']) && !empty($_GET['var_a'])) ? $_GET['var_a'] : $this->data['url']['var_a'];
        $this->data['url']['var_b'] = (isset($_GET['var_b']) && !empty($_GET['var_a'])) ? $_GET['var_b'] : $this->data['url']['var_b'];
        $this->data['url']['var_c'] = (isset($_GET['var_c']) && !empty($_GET['var_a'])) ? $_GET['var_c'] : $this->data['url']['var_c'];

        $invalid = array(
            "widget",
            "install"
        );

        if (in_array($this->data['url']['action'], $invalid))
            $this->data['url']['action'] = "main";

        foreach ($_GET as $name => $value) {
            if ($name != "controller" || $name != "action")
                $this->data[$name] = $value;
        }
    }

    public function loadConfig($file) {
        $config = array();
        if (file_exists(PATH_CONFIGS . $file))
            include(PATH_CONFIGS . $file);
        if (!empty($config)) {
            foreach ($config as $name => $array) {
                $this->configs[$name] = $array;
            }
        }
    }

    public function setMainTemplate($template) {
        $this->main_template = $template;
    }

    public function build() {
        $pref = new Pref("system");
        $this->setMainTemplate($pref->template);

        $tpl = new Template(PATH_TEMPLATES);
        if (!USER_ID && !$this->isAllowed())
            header("location: " . page("user", "login"));

        if (!USER_ID && $this->isAllowed())
            $tpl->login = true;
        else
            $tpl->login = false;
        $tpl->loadFile("template.php");
        $tpl->data = $this->data;
        $tpl->build();
    }

    private function isAllowed() {
        $allowed = array(
            "rss" => array("feed"),
            "torrent" => array("download"),
            "user" => array("login", "register", "confirm", "recover", "invite")
        );
        if (!isset($allowed[$this->data['url']['application']]))
            return false;
        if (in_array($this->data['url']['action'], $allowed[$this->data['url']['application']]))
            return true;
        else
            return false;
    }

}

?>