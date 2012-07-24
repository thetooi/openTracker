<?php

ob_start();

ini_set("display_errors", true);
error_reporting(E_ALL);

include("rootSettings.php");
if (!file_exists(PATH_CONFIGS . "database.php"))
    header("location: setup/");


include(PATH_LIBRARY . "Main.php");
include(PATH_ROOT . "update.php");


$pref = new Pref("website");

define("INCLUDED", true);
define("CLEAN_URLS", $pref->cleanurls);
define("DEFAULT_LANGUAGE", $pref->language);
define("COOKIE_PREFIX", "opentracker_");
define("SYSTEM_VERSION", "0.2.0");
define("START_APP", $pref->startapp);
define("CMS_URL", (isset($_SERVER['HTTPS']) ? "https://" : "http://") . str_replace(array("http://", "https://"), array("", ""), $pref->url));

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

$page_title = "";

$db = new DB;
$db->query("SELECT COUNT(user_id) as users FROM {PREFIX}users");
$db->nextRecord();
define("SYSTEM_USERS", $db->users);


if (USER_ID) {
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

function _tCurrentFilename($fullPath) {
    $currentFilename = str_replace(PATH_ROOT, "", $fullPath);
    return $currentFilename;
}

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

    return $text;
}

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

function error($msg) {
    return "<div class = 'ui-widget'>
        <div class = 'ui-state-error ui-corner-all' style = 'padding: 0 .7em;'>
        <p><span class = 'ui-icon ui-icon-alert' style = 'float: left; margin-right: .3em;'></span>" . $msg . "</p>
        </div>
        </div>";
}

function notice($msg) {
    return "
        <div class = 'ui-widget'>
        <div class = 'ui-state-highlight ui-corner-all' style = 'margin-top: 20px; padding: 0 .7em;'>
        <p><span class = 'ui-icon ui-icon-info' style = 'float: left; margin-right: .3em;'></span>" . $msg . "</p>
        </div>
        </div>
        ";
}

function cleanquotes(&$in) {
    if (is_array($in))
        return array_walk($in, 'cleanquotes');
    return $in = stripslashes($in);
}

function benc_resp($d) {
    benc_resp_raw(benc(array('type' => 'dictionary', 'value' => $d)));
}

function benc_resp_raw($x) {
    header("Content-Type: text/plain");
    header("Pragma: no-cache");

    if ($_SERVER['HTTP_ACCEPT_ENCODING'] == 'gzip') {
        header("Content-Encoding: gzip");
        echo gzencode($x, 9, FORCE_GZIP);
    }
    else
        echo $x;
}

function benc($obj) {
    if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
        return;
    $c = $obj["value"];
    switch ($obj["type"]) {
        case "string":
            return benc_str($c);
        case "integer":
            return benc_int($c);
        case "list":
            return benc_list($c);
        case "dictionary":
            return benc_dict($c);
        default:
            return;
    }
}

function benc_str($s) {
    return strlen($s) . ":$s";
}

function benc_int($i) {
    return "i" . $i . "e";
}

function benc_list($a) {
    $s = "l";
    foreach ($a as $e) {
        $s .= benc($e);
    }
    $s .= "e";
    return $s;
}

function benc_dict($d) {
    $s = "d";
    $keys = array_keys($d);
    sort($keys);
    foreach ($keys as $k) {
        $v = $d[$k];
        $s .= benc_str($k);
        $s .= benc($v);
    }
    $s .= "e";
    return $s;
}

function bdec_file($f, $ms) {
    $fp = fopen($f, "rb");
    if (!$fp)
        return;
    $e = fread($fp, $ms);
    fclose($fp);
    return bdec($e);
}

function bdec($s) {
    if (preg_match('/^(\d+):/', $s, $m)) {
        $l = $m[1];
        $pl = strlen($l) + 1;
        $v = substr($s, $pl, $l);
        $ss = substr($s, 0, $pl + $l);
        if (strlen($v) != $l)
            return;
        return array("type" => "string", "value" => $v, "strlen" => strlen($ss), "string" => $ss);
    }
    if (preg_match('/^i(-{0,1}\d+)e/', $s, $m)) {
        $v = $m[1];
        $ss = "i" . $v . "e";
        if ($v === "-0")
            return;
        if ($v[0] == "0" && strlen($v) != 1)
            return;
        return array("type" => "integer", "value" => $v, "strlen" => strlen($ss), "string" => $ss);
    }
    switch ($s[0]) {
        case "l":
            return bdec_list($s);
        case "d":
            return bdec_dict($s);
        default:
            return;
    }
}

function bdec_dict($s) {
    if ($s[0] != "d")
        return;
    $sl = strlen($s);
    $i = 1;
    $v = array();
    $ss = "d";
    for (;;) {
        if ($i >= $sl)
            return;
        if ($s[$i] == "e")
            break;
        $ret = bdec(substr($s, $i));
        if (!isset($ret) || !is_array($ret) || $ret["type"] != "string")
            return;
        $k = $ret["value"];
        $i += $ret["strlen"];
        $ss .= $ret["string"];
        if ($i >= $sl)
            return;
        $ret = bdec(substr($s, $i));
        if (!isset($ret) || !is_array($ret))
            return;
        $v[$k] = $ret;
        $i += $ret["strlen"];
        $ss .= $ret["string"];
    }
    $ss .= "e";
    return array("type" => "dictionary", "value" => $v, "strlen" => strlen($ss), "string" => $ss);
}

function bdec_list($s) {
    if ($s[0] != "l")
        return;
    $sl = strlen($s);
    $i = 1;
    $v = array();
    $ss = "l";
    for (;;) {
        if ($i >= $sl)
            return;
        if ($s[$i] == "e")
            break;
        $ret = bdec(substr($s, $i));
        if (!isset($ret) || !is_array($ret))
            return;
        $v[] = $ret;
        $i += $ret["strlen"];
        $ss .= $ret["string"];
    }
    $ss .= "e";
    return array("type" => "list", "value" => $v, "strlen" => strlen($ss), "string" => $ss);
}

function dict_check($d, $s) {
    if ($d["type"] != "dictionary")
        throw new Exception("not Dict.");
    $a = explode(":", $s);
    $dd = $d["value"];
    $ret = array();
    $t = '';
    foreach ($a as $k) {
        unset($t);
        if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
            $k = $m[1];
            $t = $m[2];
        }
        if (!isset($dd[$k]))
            throw new Exception("No Keys found.");
        if (isset($t)) {
            if ($dd[$k]["type"] != $t)
                throw new Exception("Invalid entry key.");
            $ret[] = $dd[$k]["value"];
        }
        else
            $ret[] = $dd[$k];
    }
    return $ret;
}

function dict_get($d, $k, $t) {
    if ($d["type"] != "dictionary")
        throw new Exception("not Dict.");
    $dd = $d["value"];
    if (!isset($dd[$k]))
        return;
    $v = $dd[$k];
    if ($v["type"] != $t)
        throw new Exception("Unknown type.");
    return $v["value"];
}

function searchfield($s) {
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function hash_where($name, $hash) {
    $db = new DB;
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = '" . $db->escape($hash) . "' OR $name = '" . $db->escape($shhash) . "')";
}

function file_list($arr, $id) {
    $db = new DB;
    foreach ($arr as $v)
        $new[] = "('$id', '" . $db->escape($v[0]) . "', " . $v[1] . ")";
    return join(",", $new);
}

function nfostrip($str) {
    $match = array("/[^a-zA-Z0-9-+., &=??????:;
        *'\"???\/\@\[\]\(\)\s]/",
        "/((\x0D\x0A\s*){3,}|(\x0A\s*){3,}|(\x0D\s*){3,})/",
        "/\x0D\x0A|\x0A|\x0D/");
    $replace = array("", "\n", "\n");
    $str = preg_replace($match, $replace, trim($str));
    return $str;
}

function pager($rpp, $count, $href, $arg = "", $opts = array(), $sign = "?") {
    if ($rpp > $count)
        return array('pagertop' => ' &nbsp;
        ', 'pagerbottom' => ' &nbsp;
        ', 'limit' => $rpp);

    $pages = ceil($count / $rpp);

    if (!isset($opts["lastpagedefault"]))
        $pagedefault = 0;
    else {
        $pagedefault = floor(($count - 1) / $rpp);
        if ($pagedefault < 0)
            $pagedefault = 0;
    }

    if (isset($_GET["page"])) {
        $page = 0 + $_GET["page"];
        if ($page < 0)
            $page = $pagedefault;
    }
    else
        $page = $pagedefault;

    $pager = "";
    $mp = $pages - 1;
    $as = " &lt;&lt; " . _t("Previous");
    if ($page >= 1) {
        $pager .= "<a href=\"{$href}{$sign}page=" . ($page - 1) . $arg . "\"><b>";
        $pager .= $as;
        $pager .= "</b></a>";
    }
    else
        $pager .= "<span>" . $as . "</span>";


    $pager .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    $as = _t("Next") . " &gt; &gt;";
    if ($page < $mp && $mp >= 0) {
        $pager .= "<a href=\"{$href}{$sign}page=" . ($page + 1) . $arg . "\"><b>";
        $pager .= $as;
        $pager .= "</b></a>";
    }
    else
        $pager .= "<span>" . $as . "</span>";

    if ($count) {
        $pagerarr = array();
        $dotted = 0;
        $dotspace = 2;
        $dotend = $pages - $dotspace;
        $curdotend = $page - $dotspace;
        $curdotstart = $page + $dotspace;
        for ($i = 0; $i < $pages; $i++) {
            if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
                if (!$dotted)
                    $pagerarr[] = "&nbsp;&nbsp;<b>...</b>&nbsp;&nbsp; | ";
                $dotted = 1;
                continue;
            }
            $dotted = 0;
            $start = $i * $rpp + 1;
            $end = $start + $rpp - 1;
            if ($end > $count)
                $end = $count;
            $text = "$start&nbsp;-&nbsp;$end";
            if ($i != $page)
                $pagerarr[] = "<a href=\"{$href}{$sign}page=$i$arg \">$text</a> | ";
            else
                $pagerarr[] = "<span>$text | </span>";
        }
        $pagerstr = join(" ", $pagerarr);
        $pagertop = "<p align=\"center\" class='pages' >$pager<br /> $pagerstr</p>\n";
        $pagerbottom = "<p align=\"center\" class='pages' >$pager<br /> $pagerstr</p>\n";
    }
    else {
        $pagertop = "<p align=\"center\">$pager</p>\n";
        $pagerbottom = $pagertop;
    }

    $start = $page * $rpp;
    return array('pagertop' => $pagertop, 'pagerbottom' => $pagerbottom, 'limit' => $start . "," . $rpp);
}

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

function bbcodes($s) {

    $acl = new Acl(USER_ID);

    $s = preg_replace("#\[h1\]((\s|.)+?)\[\/h1\]#is", "<h1>\\1</h1>", $s);
    $s = preg_replace("#\[h2\]((\s|.)+?)\[\/h2\]#is", "<h2>\\1</h2>", $s);
    $s = preg_replace("#\[h3\]((\s|.)+?)\[\/h3\]#is", "<h3>\\1</h3>", $s);
    $s = preg_replace("#\[h4\]((\s|.)+?)\[\/h4\]#is", "<h4>\\1</h4>", $s);

    $s = str_replace(array("[ul]", "[/ul]"), array("<ul>", "</ul>"), $s);

    // [li]li[/li]
    $s = preg_replace("#\[li\]((\s|.)+?)\[\/li\]#is", "<li>\\1</li>", $s);

    // [b]Bold[/b]
    $s = preg_replace("#\[b\]((\s|.)+?)\[\/b\]#is", "<b>\\1</b>", $s);

    // [i]Italic[/i]
    $s = preg_replace("#\[i\]((\s|.)+?)\[\/i\]#is", "<i>\\1</i>", $s);

    // [u]Underline[/u]
    $s = preg_replace("#\[u\]((\s|.)+?)\[\/u\]#is", "<u>\\1</u>", $s);

    // [u]Underline[/u]
    $s = preg_replace("#\[u\]((\s|.)+?)\[\/u\]#is", "<u>\\1</u>", $s);

    // [img]http://www/image.gif[/img]
    $s = preg_replace("#\[img\](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))\[\/img\]#is", "<img border=\"0\" src=\"\\1\" alt='' />", $s);

    // [img=http://www/image.gif]
    $s = preg_replace("#\[img=(http:\/\/[^\s'\"<>]+(\.(gif|jpg|png)))\]\[\/img\]#is", "<img border=\"0\" src=\"\\1\" alt='' />", $s);

    // [color=blue]Text[/color]
    // [color=#ffcc99]Text[/color]
    $s = preg_replace("/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i", "<font color='\\1'>\\2</font>", $s);

    $s = preg_replace("#\[color=(\#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]#is", "<font color='\\1'>\\2</font>", $s);

    // [url=http://www.example.com]Text[/url]
    $s = preg_replace("#\[url=([^()<>\s]+?)\]((\s|.)+?)\[\/url\]#is", "<a href=\"\\1\" target=\"_blank\">\\2</a>", $s);

    // [url]http://www.example.com[/url]
    $s = preg_replace("#\[url\]([^()<>\s]+?)\[\/url\]#is", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $s);

    // [size=4]Text[/size]
    $s = preg_replace("#\[size=([1-7])\]((\s|.)+?)\[\/size\]#is", "<font size='\\1'>\\2</font>", $s);

    // [font=Arial]Text[/font]
    $s = preg_replace("#\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]#is", "<font face=\"\\1\">\\2</font>", $s);

    $s = format_quotes($s);

    return $s;
}

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

function format_quotes($s) {
    $old_s = '';
    while ($old_s != $s) {
        $old_s = $s;

        //find first occurrence of [/quote]
        $close = strpos($s, "[/quote]");
        if ($close === false)
            return $s;

        //find last [quote] before first [/quote]
        //note that there is no check for correct syntax
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

function findyoutube($url) {
    parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
    return $my_array_of_vars['v'];
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

function htmlformat($text, $bbcodes = false) {
    $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
    $text = nl2br($text);
    if ($bbcodes) {
        $text = bbcodes($text);
        $text = parsesmileys($text);
    }
    return $text;
}

function bbcode_buttons($id) {

    $btn = "<img src='images/bbcode/bold.png' onclick='javascript:addtag(\"$id\", \"b\");' \>";
    $btn .= "<img src='images/bbcode/italic.png' onclick='javascript:addtag(\"$id\", \"i\");' \>";
    $btn .= "<img src='images/bbcode/underline.png' onclick='javascript:addtag(\"$id\", \"u\");' \>";
    $btn .= "<img src='images/bbcode/quote.png' onclick='javascript:addtag(\"$id\", \"quote\");' \>";
    $btn .= "<img src='images/bbcode/img.png' onclick='javascript:addtag(\"$id\", \"img\");' \>";
    $btn .= "<img src='images/bbcode/url.png' onclick='javascript:addtag(\"$id\", \"url\");' \>";

    return $btn;
}

function validate_string($string) {
    if (preg_match('/[^a-zA-Z0-9_]/', $string) == 0) {
        return true;
    } else {
        return false;
    }
}

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

function bbeditor($name, $rows = 5, $cols = 50, $content = "") {
    $b = "<table class='bbedit' cellpadding='0' cellspacing='0'>";
    $b .= "<tr><td class='toolbar'>" . bbcode_buttons($name) . "<td></tr>";
    $b .= "<tr><td class='editarea'><textarea id = '$name' rows = '$rows' cols = '$cols' name = '$name'>$content</textarea><td></tr>";
    $b .= "<tr><td class='smilies'>" . displaysmileys($name) . "<td></tr>";
    $b .= "</table>";
    return $b;
}

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
