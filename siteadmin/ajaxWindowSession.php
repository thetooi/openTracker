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

session_start();
/**
 * New logging instance
 */
/* Get action */
$action = $_POST['action'];
/* Get which window */
$div_id = (isset($_POST['div_id'])) ? $_POST['div_id'] : null;
/* Value of the window (open or closed) */
$open = (isset($_POST['open'])) ? $_POST['open'] : null;
echo "AJAXOK";
/**
 * This function checks if window that alaways
 * should be opened exists in the open window
 * array.
 * 
 * @param $needles Sesssions IDs to look for
 * in the array
 * @param $haystack The array to look in
 * 
 * @return bool True if found. False otherwise.
 */
function mustOpenExists($needles, $haystack) {

    foreach ($needles as $needle) {

        if (array_key_exists($needle, $haystack)) {

            return true;
        }
    }

    return false;
}

/**
 * Checks if this session variable is set
 * and what the value is. Simply it checks
 * if the window is open or not.
 * 
 * @param $div The id of the window
 * 
 * @return string ID_of_the_window|open/none
 */
function getWindowSessionWithId($div) {

    if (isset($_SESSION['session_open_div'][$div])) {

        return $div . "|" . $_SESSION['session_open_div'][$div];
    } else {

        return $div . "|none";
    }
}

/**
 * An array for window that always must be
 * open. So there always is SOMETHING to click.
 */
$mustOpenWindows = array("main_menu_links");

/**
 * $ch is arrays for storing children of
 * windows. And $pa is for storing parents
 * of windows.
 */

$ch = array();
$pa = array();

/**
 * Array with window IDs that should not be
 * opened at loading of the adminstration.
 */
$notAtLoad = array();

/**
 * Check which action and fire correct
 * function / action.
 */
switch ($action) {

    /**
     * This action is for setting a window
     * session.
     */
    case "window_session":

        if ($open == 1) {

            $_SESSION['session_open_div'][$div_id] = "block";
        } else {

            $_SESSION['session_open_div'][$div_id] = "none";
        }

        echo $open;

        break;

    /**
     * This action is for checking of the
     * window was opened earlier or if
     * it's closed.
     */
    case "lookup_window_session":
        if (isset($_SESSION['session_open_div'][$div_id])) {
            echo $_SESSION['session_open_div'][$div_id];
        } else {

            echo "AJAXOKnone";
        }

        break;

    /**
     * Echo an array of all windows that are open.
     */
    case "get_open_window_sessions":

        /* Second argument in mustOpenExists function must be an array */
        $session_open_div = ( isset($_SESSION['session_open_div']) ) ? $_SESSION['session_open_div'] : array();

        /* If any windows are open */
        if (isset($_SESSION['session_open_div']) && count($_SESSION['session_open_div'] > 0)) {

            $value_array = array();
            foreach ($_SESSION['session_open_div'] as $key => $val) {

                if (($val == "block" && !in_array($key, $notAtLoad)) || $val == "force") {

                    $value_array[] = $key;
                }
            }

            foreach ($value_array as $key => $div_id) {

                if (isset($pa[$div_id])) {
                    foreach ($pa[$div_id] as $parent_id) {

                        if ($_SESSION['session_open_div'][$parent_id] == "none") {

                            unset($value_array[$key]);
                        }
                    }
                }
            }

            $return_string = implode("::", $value_array);

            echo $return_string;
        }


        break;

    /**
     * Find the children of a window.
     */
    case "get_children":

        $return_string = implode("::", $ch[$div_id]);
        //echo $return_string."::".$_SESSION['session_open_div']['window_text_2'];
        echo $return_string;

        break;
}
?>