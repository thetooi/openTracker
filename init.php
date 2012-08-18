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
/**
 * filename init.php
 * 
 * @author Wuild
 * @package openTracker
 */
ob_start();

ini_set("display_errors", true);
error_reporting(E_ALL);

include("rootSettings.php");
if (!file_exists(PATH_CONFIGS . "database.php"))
    header("location: setup/");

include(PATH_LIBRARY . "Main.php");
include(PATH_ROOT . "update.php");


$pref = new Pref("website");

$url = $pref->url;
if (substr($url, -1) != "/")
    $url .= "/";
$url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . str_replace(array("http://", "https://"), array("", ""), $url);


/**
 * check if init.php is included
 */
define("INCLUDED", true);

/**
 * Check if cleanurls is active 
 */
define("CLEAN_URLS", $pref->cleanurls);

/**
 * System default language 
 */
define("DEFAULT_LANGUAGE", $pref->language);

/**
 * Cookie prefix
 * @TODO add COOKIE_PREFIX to a config file. 
 */
define("COOKIE_PREFIX", "opentracker_");

/**
 * System version 
 */
define("SYSTEM_VERSION", "0.2.1");

/**
 * Start page 
 */
define("START_APP", $pref->startapp);

/**
 * base url. 
 */
define("CMS_URL", $url);

if (get_magic_quotes_gpc()) {
    array_walk($_GET, 'cleanquotes');
    array_walk($_POST, 'cleanquotes');
    array_walk($_COOKIE, 'cleanquotes');
    array_walk($_REQUEST, 'cleanquotes');
}

$token = md5(uniqid(rand(), TRUE));
$token2 = md5(uniqid(rand(), TRUE));
if (!isset($_SESSION['secure_token_last'])) {
    $_SESSION['secure_token'] = $token;
    $_SESSION['secure_token_last'] = $token;
} else {
    $token = md5(uniqid(rand(), TRUE));
    $_SESSION['secure_token'] = $_SESSION['secure_token_last'];
    $_SESSION['secure_token_last'] = $token;
}

/*
 * Defining USER_ID 
 * If not logged in set USER_ID = false
 * else
 * Set USER_ID = logged in user_id
 */
if (isset($_COOKIE[COOKIE_PREFIX . 'user'])) {
    $cookie_vars = explode(".", $_COOKIE[COOKIE_PREFIX . 'user']);
    $cookie_1 = $cookie_vars['0'];
    $cookie_2 = $cookie_vars['1'];
    $userdata = new DB("users");
    $userdata->setColPrefix("user_");
    $id = $userdata->escape($cookie_1);
    $password = $userdata->escape($cookie_2);
    $userdata->select("user_id = '" . $id . "'");
    if ($userdata->numRows()) {
        $userdata->nextRecord();

        if ($userdata->status == 0 || $userdata->status == 1 || $userdata->status == 3)
            header("location: " . page("user", "logout"));

        if (md5("!" . $userdata->id . md5("!" . $userdata->password . md5($_SERVER['REMOTE_ADDR']))) == $password) {
            define("USER_ID", $userdata->id);
        } else {
            define("USER_ID", false);
        }
    } else {
        define("USER_ID", false);
    }
} else {
    define("USER_ID", false);
}

/*
 * Counts total users, 
 */
$db = new DB;
$db->query("SELECT COUNT(user_id) as users FROM {PREFIX}users");
$db->nextRecord();
define("SYSTEM_USERS", $db->users);

/*
 * IF USER_ID != false
 * Get users current language
 */
if (USER_ID != false) {
    $db = new DB("users");
    $db->user_last_access = time();
    $db->update("user_id = '" . USER_ID . "'");

    $acl = new Acl(USER_ID);
    $db = new DB("system_languages");
    $db->select("language_id = '" . $acl->language . "'");
    if ($db->numRows())
        define("CURRENT_LANGUAGE", $acl->language);
    else
        define("CURRENT_LANGUAGE", DEFAULT_LANGUAGE);
}else {
    define("CURRENT_LANGUAGE", DEFAULT_LANGUAGE);
}

/**
 * __autoload
 * 
 * Loads class files from PATH_LIBRARY automaticly on use.
 * 
 * @param string $classname class name to load
 * 
 */
function __autoload($classname) {
    $class_exploded = explode("_", $classname);
    if (count($class_exploded) == 1) {
        if (file_exists(PATH_LIBRARY . $classname . ".php"))
            include_once(PATH_LIBRARY . $classname . ".php");
    }else {
        if (file_exists(PATH_APPLICATIONS . $class_exploded[0] . "/library/" . $class_exploded[1] . ".php"))
            include_once(PATH_APPLICATIONS . $class_exploded[0] . "/library/" . $class_exploded[1] . ".php");
        else
            die("could not load library " . PATH_APPLICATIONS . $class_exploded[0] . "/library/" . $class_exploded[1] . ".php");
    }
}

/**
 * _t
 * 
 * Converts phrases into translatable strings
 * 
 * @param string $phrase The phrase to be translated
 * @return string Returns the translated string
 */
function _t($phrase) {
    $translate = new DB("translation");
    $translate->select("translation_lang_id = '" . CURRENT_LANGUAGE . "' AND translation_phrase = '" . $translate->escape($phrase) . "'");
    if ($translate->numRows()) {
        $translate->nextRecord();
        if ($translate->translation_phrase_translated == "") {
            $text = $translate->translation_phrase;
        } else {
            $text = $translate->translation_phrase_translated;
        }
    } else {
        if (CURRENT_LANGUAGE == DEFAULT_LANGUAGE) {
            $translate->translation_lang_id = DEFAULT_LANGUAGE;
            $translate->translation_phrase = $phrase;
            $translate->translation_phrase_translated = "";
            $translate->insert();
        }
        $text = $phrase;
    }
    $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
    return $text;
}

/**
 * Check if the selected $port is blacklisted
 * 
 * @param int $port port number
 * @return boolean true/false
 */
function blacklist($port) {
    // direct connect
    if ($port >= 411 && $port <= 413)
        return true;
    // bittorrent
    if ($port >= 6881 && $port <= 6889)
        return true;
    // kazaa
    if ($port == 1214)
        return true;
    // gnutella
    if ($port >= 6346 && $port <= 6347)
        return true;
    // emule
    if ($port == 4662)
        return true;
    // winmx
    if ($port == 6699)
        return true;
    return false;
}

/**
 * Returns the correct page url to the selected application
 *  
 * @param string $app Selected application
 * @param string $action action file inside the application
 * @param string $var_a alternative variable a
 * @param string $var_b alternative variable b
 * @param string $var_c alternative variable c
 * @param string $alt alternative variable
 * @return string return the url
 */
function page($app, $action = "", $var_a = "", $var_b = "", $var_c = "", $alt = "") {
    $url = "";
    $app = cleanurl($app);
    $action = cleanurl($action);
    $var_a = cleanurl($var_a);
    $var_b = cleanurl($var_b);
    $var_c = cleanurl($var_c);
    if (CLEAN_URLS) {
        $url .= CMS_URL . "$app/";
        if (!empty($action))
            $url .= "$action/";
        if (!empty($var_a))
            $url .= "$var_a/";
        if (!empty($var_b))
            $url .= "$var_b/";
        if (!empty($var_c))
            $url .= "$var_c/";
        if (!empty($alt))
            $url .= "?" . $alt;
    } else {
        $url .= CMS_URL . "index.php?application=$app";
        if (!empty($action))
            $url .= "&action=$action";
        if (!empty($var_a))
            $url .= "&var_a=$var_a";
        if (!empty($var_b))
            $url .= "&var_b=$var_b";
        if (!empty($var_c))
            $url .= "&var_c=$var_c";
        if (!empty($alt))
            $url .= "&" . $alt;
    }
    return $url;
}

/**
 * Convert bytes to a more user friendly string
 * 
 * @param int $bytes the number of bytes to convert
 * @return string Returns the data
 */
function bytes($bytes) {
    if ($bytes < 1000 * 1024)
        return number_format($bytes / 1024, 2) . " kB";
    elseif ($bytes < 1000 * 1048576)
        return number_format($bytes / 1048576, 2) . " MB";
    elseif ($bytes < 1000 * 1073741824)
        return number_format($bytes / 1073741824, 2) . " GB";
    else
        return number_format($bytes / 1099511627776, 2) . " TB";
}

/**
 * Displays an error message with jquery ui
 * 
 * @param string $msg the message to display
 * @return string The error message
 */
function error($msg) {
    return "<div class = 'ui-widget'>
        <div class = 'ui-state-error ui-corner-all' style = 'padding: 0 .7em;'>
        <p><span class = 'ui-icon ui-icon-alert' style = 'float: left; margin-right: .3em;'></span>" . $msg . "</p>
        </div>
        </div>";
}

/**
 * Displays a notice message with jquery ui
 * 
 * @param string $msg the message to display
 * @return string The notice message
 */
function notice($msg) {
    return "
        <div class = 'ui-widget'>
        <div class = 'ui-state-highlight ui-corner-all' style = 'margin-top: 20px; padding: 0 .7em;'>
        <p><span class = 'ui-icon ui-icon-info' style = 'float: left; margin-right: .3em;'></span>" . $msg . "</p>
        </div>
        </div>
        ";
}

/**
 * Clean quotes
 * 
 * @param array $in
 */
function cleanquotes($in) {
    if (is_array($in))
        return array_walk($in, 'cleanquotes');
    return $in = stripslashes($in);
}

/**
 * replace un wanted characters with spaces to make it more search friendly
 * 
 * @param string $s
 */
function searchfield($s) {
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

/**
 * find hash
 * @param string $name
 * @param string $hash
 * @return string 
 */
function hash_where($name, $hash) {
    $db = new DB;
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = '" . $db->escape($hash) . "' OR $name = '" . $db->escape($shhash) . "')";
}

/**
 * List files in torrent file
 * @param array $arr
 * @param string $id
 * @return type 
 */
function file_list($arr, $id) {
    $db = new DB;
    foreach ($arr as $v)
        $new[] = "('$id', '" . $db->escape($v[0]) . "', " . $v[1] . ")";
    return join(",", $new);
}

/**
 * String the nfo file from crap.
 * 
 * @param string $str the nfo content
 * @return string the new striped content
 */
function nfostrip($str) {
    $match = array("/[^a-zA-Z0-9-+., &=??????:;
        *'\"???\/\@\[\]\(\)\s]/",
        "/((\x0D\x0A\s*){3,}|(\x0A\s*){3,}|(\x0D\s*){3,})/",
        "/\x0D\x0A|\x0A|\x0D/");
    $replace = array("", "\n", "\n");
    $str = preg_replace($match, $replace, trim($str));
    return $str;
}

/**
 * Trims a string lenght to not show all characters
 * 
 * @param string $text the string
 * @param int $length the lenght of the short string
 * @return string The new shorten string
 */
function trimstr($text, $length = 50) {
    $dec = array("&", "\"", "'", "\\", '\"', "\'", "<", ">");
    $enc = array("&amp;", "&quot;", "&#39;", "&#92;", "&quot;", "&#39;", "&lt;", "&gt;");
    $text = str_replace($enc, $dec, $text);
    if (strlen($text) > $length)
        $text = substr($text, 0, ($length - 3)) . "...";
    $text = str_replace($dec, $enc, $text);
    return $text;
}

/**
 * Send an email with the noreply_email setting in the pref
 * 
 * @param string $to The address to send the email to
 * @param string $subject The subject of the email
 * @param string $body The body of the email
 * 
 * @return boolean $mail
 */
function sendEmail($to, $subject, $body) {
    $pref = new Pref("website");

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "From: " . $pref->noreply_email . "\r\n";

    $tpl = new Template(PATH_CMS . "templates/");
    $tpl->content = $body;
    $tpl->loadFile("email.tpl.php");
    $body = $tpl->buildVar();
    $mail = mail($to, $subject, $body, $headers);
    if ($mail)
        return $mail;
    else
        die("could not send email");
}

/**
 * Generate a random password string
 * 
 * @param int $length the lenght of the string
 * @return string The random password
 */
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

/**
 * Generates a time difference string.
 * 
 * @param int $date1 the first date
 * @param int $date2 the second date
 * @return string returns how many minutes/hours/days ect has been between the two dates.
 */
function timediff($date1, $date2) {
    $diff = abs($date2 - $date1);
    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
    $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
    $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
    $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));
    $return = "";
    if ($years != 0)
        $return .= $years . " years ";
    if ($months != 0)
        $return .= $months . " months ";
    if ($days != 0)
        $return .= $days . " days ";
    if ($hours != 0)
        $return .= $hours . " hours ";
    $return .= $minutes . " minutes ";
    return $return;
}

/**
 * Convert string to bbcodes.
 * 
 * @param string $s the string to convert
 * @return string the converted string
 */
function bbcodes($s) {
    $s = preg_replace("#\[h1\]((\s|.)+?)\[\/h1\]#is", "<h1>\\1</h1>", $s);
    $s = preg_replace("#\[h2\]((\s|.)+?)\[\/h2\]#is", "<h2>\\1</h2>", $s);
    $s = preg_replace("#\[h3\]((\s|.)+?)\[\/h3\]#is", "<h3>\\1</h3>", $s);
    $s = preg_replace("#\[h4\]((\s|.)+?)\[\/h4\]#is", "<h4>\\1</h4>", $s);
    $s = str_replace(array("[ul]", "[/ul]"), array("<ul>", "</ul>"), $s);
    $s = preg_replace("#\[li\]((\s|.)+?)\[\/li\]#is", "<li>\\1</li>", $s);
    $s = preg_replace("#\[b\]((\s|.)+?)\[\/b\]#is", "<b>\\1</b>", $s);
    $s = preg_replace("#\[i\]((\s|.)+?)\[\/i\]#is", "<i>\\1</i>", $s);
    $s = preg_replace("#\[u\]((\s|.)+?)\[\/u\]#is", "<u>\\1</u>", $s);
    $s = preg_replace("#\[u\]((\s|.)+?)\[\/u\]#is", "<u>\\1</u>", $s);
    $s = preg_replace("#\[img\](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))\[\/img\]#is", "<img border=\"0\" src=\"\\1\" alt='' />", $s);
    $s = preg_replace("#\[img=(http:\/\/[^\s'\"<>]+(\.(gif|jpg|png)))\]\[\/img\]#is", "<img border=\"0\" src=\"\\1\" alt='' />", $s);
    $s = preg_replace("/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i", "<font color='\\1'>\\2</font>", $s);
    $s = preg_replace("#\[color=(\#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]#is", "<font color='\\1'>\\2</font>", $s);
    $s = preg_replace("#\[url=([^()<>\s]+?)\]((\s|.)+?)\[\/url\]#is", "<a href=\"\\1\" target=\"_blank\">\\2</a>", $s);
    $s = preg_replace("#\[url\]([^()<>\s]+?)\[\/url\]#is", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $s);
    $s = preg_replace("#\[size=([1-7])\]((\s|.)+?)\[\/size\]#is", "<font size='\\1'>\\2</font>", $s);
    $s = preg_replace("#\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]#is", "<font face=\"\\1\">\\2</font>", $s);
    $s = format_quotes($s);
    return $s;
}

/**
 * Find last position
 * @param type $haystack
 * @param type $needle
 * @param type $offset
 * @return type 
 */
function _strlastpos($haystack, $needle, $offset = 0) {
    $addLen = strlen($needle);
    $endPos = $offset - $addLen;
    while (true) {
        if (($newPos = strpos($haystack, $needle, $endPos + $addLen)) === false)
            break;
        $endPos = $newPos;
    }
    return ($endPos >= 0) ? $endPos : false;
}

/**
 * Format quotes
 * @param string $s
 * @return string 
 */
function format_quotes($s) {
    $old_s = '';
    while ($old_s != $s) {
        $old_s = $s;
        $close = strpos($s, "[/quote]");
        if ($close === false)
            return $s;
        $open = _strlastpos(substr($s, 0, $close), "[quote");
        if ($open === false)
            return $s;

        $quote = substr($s, $open, $close - $open + 8);

        //[quote]Text[/quote]
        $quote = preg_replace("/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i", "<div class='citation'>\\1</div><br />", $quote);

        //[quote=Author]Text[/quote]
        $quote = preg_replace(
                "/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i", "<div class='citation'><b>\\1 said</b>: <div class='blockquote'>\\2</div></div><br />", $quote);

        $s = substr($s, 0, $open) . $quote . substr($s, $close + 8);
    }

    return $s;
}

/**
 * List all available timezones.
 * @param int $sel
 * @return string 
 */
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

/**
 * Find the youtube link
 * @param string $url
 * @return string 
 */
function findyoutube($url) {
    parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
    return $my_array_of_vars['v'];
}

/**
 * Show the date in a nice way
 * @param string $date
 * @param string $method
 * @param type $norelative
 * @param int $full_relative
 * @return string 
 */
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


    $months = array(
        "January" => _t("January"),
        "February" => _t("February"),
        "March" => _t("March"),
        "April" => _t("April"),
        "May" => _t("May"),
        "June" => _t("June"),
        "July" => _t("July"),
        "August" => _t("August"),
        "September" => _t("September"),
        "October" => _t("October"),
        "November" => _t("November"),
        "December" => _t("December"),
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
        $test = gmdate($time_options[$method], ($date + $offset));
        foreach ($months as $month => $trans) {
            $test = str_replace($month, $trans, $test);
        }

        return $test;
    }
}

/**
 * Strip the url string from unwanted characters
 * @param string $url
 * @return string 
 */
function cleanUrl($url) {
    // Replace white space & special chars to "simple"
    $arr_chars = array(' ', 'Ã§', 'Ã±', 'Å¡', 'Å¾', 'Â¢', 'Âµ', 'Ã—', 'ÃŸ');

    $arr_chars_replace = array('-', 'c', 'n', 's', 'z', 'c', 'u', 'x', 'ss');

    $url = str_ireplace($arr_chars, $arr_chars_replace, $url);

    $arr_chars = array('([Ã¥Ã¤Ã¡Ã Ã¢Ã£])',
        '([Å“Ã¦])',
        '([Ã¨Ã©ÃªÃ«])',
        '([Ã¬Ã­Ã®Ã¯])',
        '([Ã¹ÃºÃ»Ã¼])',
        '([Ã½Ã¿])',
        '([Ã°Ã²Ã³Ã´ÃµÃ¶Ã¸])');

    $arr_chars_replace = array('a',
        'ae',
        'e',
        'i',
        'u',
        'y',
        'o');

    $url = preg_replace($arr_chars, $arr_chars_replace, $url);

    $allowed_chars = "/[^a-z0-9_-]/i";

    $url = preg_replace($allowed_chars, '', $url);


    return strtolower($url);
}

/**
 * Make file or folder list of selected folder.
 * @param string $folder
 * @param string $filter
 * @param boolean $sort
 * @param string $type
 * @param string $ext_filter
 * @return array
 */
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

/**
 * Make options from an array
 * @param array $files
 * @param string $selected
 * @return string 
 */
function makefileopts($files, $selected = "") {
    $res = "";
    for ($i = 0; $i < count($files); $i++) {
        $sel = ($selected == $files[$i] ? " selected='selected'" : "");
        $res .= "<option value='" . $files[$i] . "'$sel>" . $files[$i] . "</option>\n";
    }
    return $res;
}

/**
 * Format html code and add bbcode if $bbcode = true
 * @param string $text
 * @param boolean $bbcodes
 * @return string 
 */
function htmlformat($text, $bbcodes = false) {
    $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
    $text = nl2br($text);
    if ($bbcodes) {
        $text = bbcodes($text);
        $text = parsesmileys($text);
    }
    return $text;
}

/**
 * Display bbcode buttons
 * @param string $id
 * @return string 
 */
function bbcode_buttons($id) {
    $btn = "<img src='images/bbcode/bold.png' onclick='javascript:addtag(\"$id\", \"b\");' \>";
    $btn .= "<img src='images/bbcode/italic.png' onclick='javascript:addtag(\"$id\", \"i\");' \>";
    $btn .= "<img src='images/bbcode/underline.png' onclick='javascript:addtag(\"$id\", \"u\");' \>";
    $btn .= "<img src='images/bbcode/quote.png' onclick='javascript:addtag(\"$id\", \"quote\");' \>";
    $btn .= "<img src='images/bbcode/img.png' onclick='javascript:addtag(\"$id\", \"img\");' \>";
    $btn .= "<img src='images/bbcode/url.png' onclick='javascript:addtag(\"$id\", \"url\");' \>";

    return $btn;
}

/**
 * Validate a string to not have any unwelcome characters
 * @param type $string
 * @return boolean 
 */
function validate_string($string) {
    if (preg_match('/[^a-zA-Z0-9_]/', $string) == 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get user_id from a username
 * @param string $name
 * @return int 
 */
function getID($name) {
    $db = new DB("users");
    $db->select("user_name = '" . $db->escape($name) . "'");
    if ($db->numRows()) {
        $db->nextRecord();
        return $db->user_id;
    } else {
        return false;
    }
}

/**
 * Get all groups as selectable options
 * @param string $selected
 * @param boolean $above
 * @return string 
 */
function getGroups($selected = "", $above = false) {
    $data = "";
    $user = new Acl(USER_ID);
    $db = new DB("groups");
    $db->setColPrefix("group_");
    $db->setSort("group_id ASC");
    if ($above)
        $db->select("group_id <= $user->group");
    else
        $db->select();

    while ($db->nextRecord()) {
        if ($db->id == $selected)
            $data .= "<option value='" . $db->id . "' SELECTED>" . $db->name . "</option>";
        else
            $data .= "<option value='" . $db->id . "'>" . $db->name . "</option>";
    }
    return $data;
}

/**
 * The custom bbeditor
 * @param string $name
 * @param int $rows
 * @param int $cols
 * @param string $content
 * @return string 
 */
function bbeditor($name, $rows = 5, $cols = 50, $content = "") {
    $b = "<table class='bbedit' cellpadding='0' cellspacing='0'>";
    $b .= "<tr><td class='toolbar'>" . bbcode_buttons($name) . "<td></tr>";
    $b .= "<tr><td class='editarea'><textarea id = '$name' rows = '$rows' cols = '$cols' name = '$name'>$content</textarea><td></tr>";
    $b .= "<tr><td class='smilies'>" . displaysmileys($name) . "<td></tr>";
    $b .= "</table>";
    return $b;
}

/**
 * Display smileys in bbeditor
 * @param string $textarea
 * @return string 
 */
function displaysmileys($textarea) {
    $smiles = "";
    $smileys = array(
        ":)" => "smile.gif",
        ";)" => "wink.gif",
        ":|" => "frown.gif",
        ":(" => "sad.gif",
        ":o" => "shock.gif",
        ":p" => "pfft.gif",
        "B)" => "cool.gif",
        ":D" => "grin.gif",
        ":@" => "angry.gif"
    );
    foreach ($smileys as $key => $smiley)
        $smiles .= "<img src='images/smiley/$smiley' alt ='smiley' width='15px' height='15px' onClick=\"javascript:insertText('$textarea', '$key');\">\n";
    return $smiles;
}

/**
 * Convert smiley strings to images.
 * @param string $message
 * @return string 
 */
function parsesmileys($message) {
    $smiley = array(
        "#\:\)#si" => "<img src='images/smiley/smile.gif' alt='smiley'>",
        "#\;\)#si" => "<img src='images/smiley/wink.gif' alt='smiley'>",
        "#\:\(#si" => "<img src='images/smiley/sad.gif' alt='smiley'>",
        "#\:\|#si" => "<img src='images/smiley/frown.gif' alt='smiley'>",
        "#\:o#si" => "<img src='images/smiley/shock.gif' alt='smiley'>",
        "#\:p#si" => "<img src='images/smiley/pfft.gif' alt='smiley'>",
        "#b\)#si" => "<img src='images/smiley/cool.gif' alt='smiley'>",
        "#\:d#si" => "<img src='images/smiley/grin.gif' alt='smiley'>",
        "#\:@#si" => "<img src='images/smiley/angry.gif' alt='smiley'>"
    );
    foreach ($smiley as $key => $smiley_img)
        $message = preg_replace($key, $smiley_img, $message);
    return $message;
}

/**
 * Get all forum categories
 * @param int $selected
 * @return string 
 */
function getForumCategory($selected = "") {
    $data = "";
    $db = new DB("forum_categories");
    $db->setColPrefix("category_");
    $db->setSort("category_sort ASC");
    $db->select();
    while ($db->nextRecord()) {
        if ($db->id == $selected)
            $data .= "<option value='" . $db->id . "' SELECTED>" . $db->title . "</option>";
        else
            $data .= "<option value='" . $db->id . "'>" . $db->title . "</option>";
    }
    return $data;
}

/**
 * Get all installed languages
 * @param string $selected
 * @return string 
 */
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

/**
 * Quick function to build all the widgets.
 * @return string
 */
function buildWidgets() {
    $acl = new Acl(USER_ID);
    if (USER_ID) {
        $data = "";
        $db = new DB("widgets");
        $db->setColPrefix("widget_");
        $db->setSort("widget_sort ASC");
        $db->select("widget_group <= " . $acl->group);
        while ($db->nextRecord()) {
            $widget = new Widget($db->module);
            $data .= $widget->build();
        }

        return $data;
    }
}

new Cleanup();
?>
