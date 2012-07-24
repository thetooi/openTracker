<?php

session_start();

ini_set("display_errors", true);
error_reporting(E_ALL);


include("../rootSettings.php");
include(PATH_LIBRARY . "Template.php");
include("parser.php");

$tpl = new Template(PATH_ROOT . "setup/");

$action = (isset($_GET['action'])) ? $_GET['action'] : "";
$step = new Template(PATH_ROOT . "setup/tpl/");

$_SESSION['action'] = (isset($_SESSION['action']) ? $_SESSION['action'] : false);

switch ($action) {
    default:
        $step->loadFile("welcome.php");
        break;

    case 'step1':
        $step->loadFile("step1.php");
        break;
    case 'step2':
        $step->loadFile("step2.php");
        break;
    case 'step3':
        $step->loadFile("step3.php");
        break;
    case 'step4':
        $step->loadFile("step4.php");
        break;
    case 'step5':
        $step->loadFile("step5.php");
        break;

    case 'finish':
        $step->loadFile("finish.php");
        break;
}

function error($msg) {
    echo "<font color='red'>$msg</font>";
}

function installDB() {
    include(PATH_CONFIGS . "database.php");
    try {
        switch ($config['mysql']['type']) {
            case 'mysql':
                $mysql = @mysql_connect($config['mysql']['hostname'], $config['mysql']['username'], $config['mysql']['password']);
                if (!$mysql)
                    throw new Exception(mysql_error());
                $db = @mysql_select_db($config['mysql']['database']);
                if (!$db)
                    throw new Exception(mysql_error());
                if (file_exists(PATH_ROOT . "setup/setup.sql")) {
                    $parser = new Parser(PATH_ROOT . "setup/setup.sql");
                    $sql_query = $parser->parse();
                    foreach ($sql_query as $sql) {
                        mysql_query(str_replace("{PREFIX}", $config['mysql']['prefix'], $sql)) or die(mysql_error());
                    }
                    return true;
                }
                break;
            case 'mysqli':
                $mysql = @mysqli_connect($config['mysql']['hostname'], $config['mysql']['username'], $config['mysql']['password']);
                if (!$mysql)
                    throw new Exception(mysqli_connect_error($mysql));
                $db = @mysqli_select_db($mysql, $config['mysql']['database']);
                if (!$db)
                    throw new Exception(mysqli_error($mysql));

                if (file_exists(PATH_ROOT . "setup/setup.sql")) {
                    $parser = new Parser(PATH_ROOT . "setup/setup.sql");
                    $sql_query = $parser->parse();
                    foreach ($sql_query as $sql) {
                        mysqli_query($mysql, str_replace("{PREFIX}", $config['mysql']['prefix'], $sql)) or die(mysql_error());
                    }
                    return true;
                }
                break;
        }
    } Catch (Exception $e) {
        echo error($e->getMessage());
    }
}

function validate_string($string) {
    if (preg_match('/[^a-zA-Z0-9_]/', $string) == 0) {
        return true;
    } else {
        return false;
    }
}

function generatePassword($length = 8) {
    $password = "";
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
    $maxlength = strlen($possible);
    if ($length > $maxlength) {
        $length = $maxlength;
    }
    $i = 0;
    while ($i < $length) {
        $char = substr($possible, mt_rand(0, $maxlength - 1), 1);
        if (!strstr($password, $char)) {
            $password .= $char;
            $i++;
        }
    }
    return $password;
}

function makefilelist($folder, $filter, $sort = true, $type = "files", $ext_filter = "") {
    $res = array();
    $filter = explode("|", $filter);
    if ($type == "files" && !empty($ext_filter)) {
        $ext_filter = explode("|", strtolower($ext_filter));
    }
    $temp = opendir($folder);
    while ($file = readdir($temp)) {
        if ($type == "files" && !in_array($file, $filter)) {
            if (!empty($ext_filter)) {
                if (!in_array(substr(strtolower(stristr($file, '.')), +1), $ext_filter) && !is_dir($folder . $file)) {
                    $res[] = $file;
                }
            } else {
                if (!is_dir($folder . $file)) {
                    $res[] = $file;
                }
            }
        } elseif ($type == "folders" && !in_array($file, $filter)) {
            if (is_dir($folder . $file)) {
                $res[] = $file;
            }
        }
    }
    closedir($temp);
    if ($sort) {
        sort($res);
    }
    return $res;
}

function makefileopts($files, $selected = "") {
    $res = "";
    for ($i = 0; $i < count($files); $i++) {
        $sel = ($selected == $files[$i] ? " selected='selected'" : "");
        $res .= "<option value='" . $files[$i] . "'$sel>" . $files[$i] . "</option>\n";
    }
    return $res;
}

function getLanguages($selected) {
    $data = "";
    $db = new DB("system_languages");
    $db->setSort("language_name ASC");
    $db->select();
    while ($db->nextRecord()) {
        if ($db->language_id == $selected)
            $data .= "<option value='" . $db->language_id . "' SELECTED>" . $db->language_name . "</option>";
        else
            $data .= "<option value='" . $db->language_id . "'>" . $db->language_name . "</option>";
    }
    return $data;
}

function timezones($sel) {
    $array = array(
        "-12.0" => "(GMT -12:00) Eniwetok, Kwajalein",
        "-11.0" => "(GMT -11:00) Midway Island, Samoa",
        "-10.0" => "(GMT -10:00) Hawaii",
        "-9.0" => "(GMT -9:00) Alaska",
        "-8.0" => "(GMT -8:00) Pacific Time (US &amp; Canada)",
        "-7.0" => "(GMT -7:00) Mountain Time (US &amp; Canada)",
        "-6.0" => "(GMT -6:00) Central Time (US &amp; Canada), Mexico City",
        "-5.0" => "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima",
        "-4.0" => "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz",
        "-3.5" => "(GMT -3:30) Newfoundland",
        "-3.0" => "(GMT -3:00) Brazil, Buenos Aires, Georgetown",
        "-2.0" => "(GMT -2:00) Mid-Atlantic",
        "-1.0" => "(GMT -1:00 hour) Azores, Cape Verde Islands",
        "0.0" => "(GMT) Western Europe Time, London, Lisbon, Casablanca",
        "1.0" => "(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris",
        "2.0" => "(GMT +2:00) Kaliningrad, South Africa",
        "3.0" => "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg",
        "3.5" => "(GMT +3:30) Tehran",
        "4.0" => "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi",
        "4.5" => "(GMT +4:30) Kabul",
        "5.0" => "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent",
        "5.5" => "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
        "5.75" => "(GMT +5:45) Kathmandu",
        "6.0" => "(GMT +6:00) Almaty, Dhaka, Colombo",
        "7.0" => "(GMT +7:00) Bangkok, Hanoi, Jakarta",
        "8.0" => "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong",
        "9.0" => "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk",
        "9.5" => "(GMT +9:30) Adelaide, Darwin",
        "10.0" => "(GMT +10:00) Eastern Australia, Guam, Vladivostok",
        "11.0" => "(GMT +11:00) Magadan, Solomon Islands, New Caledonia",
        "12.0" => "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka");


    $o = "";
    foreach ($array as $time => $name) {
        $o .= "<option value='$time' " . (($time == $sel) ? "SELECTED" : "") . ">$name</option>";
    }

    return $o;
}

function get_date($date, $method = "", $norelative = 0, $full_relative = 1) {
    $pref = new Pref("time");
    $config = array();
    $offset = 60 * 60 * $pref->offset;
    $relative = 1;
    $relative_format = '{--}, H:i';

    $offset_set = 0;
    $today_time = 0;
    $yesterday_time = 0;
    $time_options = array(
        'JOINED' => $pref->joined,
        'SHORT' => $pref->short,
        'LONG' => $pref->long,
        'TINY' => $pref->tiny ? $pref->tiny : 'j M Y - G:i',
        'DATE' => $pref->date ? $pref->date : 'j M Y'
    );

    if (!$date) {
        return '--';
    }

    if (empty($method)) {
        $method = 'LONG';
    }

    if ($offset_set == 0) {
        if ($relative) {
            $today_time = gmdate('d,m,Y', ( time() + $offset));
            $yesterday_time = gmdate('d,m,Y', ( (time() - 86400) + $offset));
        }
        $offset_set = 1;
    }
    if ($relative == 3) {
        $full_relative = 1;
    }
    if ($full_relative and ( $norelative != 1 )) {
        $diff = time() - $date;
        if ($diff < 3600) {
            if ($diff < 120) {
                return '< 1 ' . _t("minute ago");
            } else {
                return sprintf('%s ' . _t("minutes ago"), intval($diff / 60));
            }
        } else if ($diff < 7200) {
            return _t("1 hour ago");
        } else if ($diff < 86400) {
            return sprintf('%s ' . _t(" hours ago"), intval($diff / 3600));
        } else if ($diff < 172800) {
            return _t("1 day ago");
        } else if ($diff < 604800) {
            return sprintf('%s ' . _t("days ago"), intval($diff / 86400));
        } else if ($diff < 1209600) {
            return _t("1 week ago");
        } else if ($diff < 3024000) {
            return sprintf('%s ' . _t("weeks ago"), intval($diff / 604900));
        } else {
            return gmdate($time_options[$method], ($date + $offset));
        }
    } else if ($relative and ( $norelative != 1 )) {
        $this_time = gmdate('d,m,Y', ($date + $offset));

        if ($relative == 2) {
            $diff = time() - $date;

            if ($diff < 3600) {
                if ($diff < 120) {
                    return '< 1 ' . _t("minute ago");
                } else {
                    return sprintf('%s ' . _t("minutes ago"), intval($diff / 60));
                }
            }
        }
        if ($this_time == $today_time) {
            return str_replace('{--}', _t("Today"), gmdate($relative_format, ($date + $offset)));
        } else if ($this_time == $yesterday_time) {
            return str_replace('{--}', _t("Yesteday"), gmdate($relative_format, ($date + $offset)));
        } else {
            return gmdate($time_options[$method], ($date + $offset));
        }
    } else {
        return gmdate($time_options[$method], ($date + $offset));
    }
}

$tpl->main_content = $step->buildVar();
$tpl->build("template.php");
?>