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
 * @package openTracker.Pager
 */

/**
 * Pager system for mysql data.
 * @package openTracker.Pager 
 */
class Pager {

    /**
     * Records per page
     * @var int 
     */
    public $perpage;

    /**
     * Total records
     * @var int
     */
    public $count;

    /**
     * App and module
     * @var array 
     */
    public $href = array("application", "module");

    /**
     * Top pager
     * @var string
     */
    public $pager_top;

    /**
     * Bottom pager
     * @var type 
     */
    public $pager_bottom;

    /**
     * Record limit
     * @var string
     */
    public $limit;

    /**
     * Link arguments
     * @var string
     */
    public $args;

    /**
     * Build the pager
     * @return boolean
     */
    public function build() {
        if ($this->perpage > $this->count)
            return array('pagertop' => ' &nbsp;', 'pagerbottom' => ' &nbsp;', 'limit' => $this->perpage);

        $pages = ceil($this->count / $this->perpage);

        $pagedefault = 0;


        if (isset($_GET["page"])) {
            $page = 0 + $_GET["page"];
            if ($page < 0)
                $page = $pagedefault;
        }
        else
            $page = $pagedefault;

        $link_sub = page($this->href[0], $this->href[1], "", "", "", "page=" . ($page - 1) . $this->args);
        $link_add = page($this->href[0], $this->href[1], "", "", "", "page=" . ($page + 1) . $this->args);


        $prev = "";
        $next = "";
        $mp = $pages - 1;
        $as = _t("Previous");
        if ($page >= 1) {
            $prev .= "<a href='$link_sub'><span class='btn blue'>$as</span></a>&nbsp;&nbsp;&nbsp;&nbsp;";
        } else
            $prev .= "<span class='btn blue disabled'>" . $as . "</span>&nbsp;&nbsp;&nbsp;&nbsp;";

        $as = _t("Next");
        if ($page < $mp && $mp >= 0) {
            $next .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href='$link_add'><span class='btn blue'>$as</span></a>";
        }
        else
            $next .= "&nbsp;&nbsp;&nbsp;&nbsp;<span class='btn blue disabled'>" . $as . "</span>";

        if ($this->count) {
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
                $link = page($this->href[0], $this->href[1], "", "", "", "page=" . ($i) . $this->args);

                $dotted = 0;
                $start = $i * $this->perpage + 1;
                $end = $start + $this->perpage - 1;
                if ($end > $this->count)
                    $end = $this->count;
                $text = "$start&nbsp;-&nbsp;$end";
                if ($i != $page)
                    $pagerarr[] = "<a href='$link'>$text</a>";
                else
                    $pagerarr[] = "<span>$text</span>";
            }
            $pagerstr = join(" | ", $pagerarr);
            $pagertop = "<p align=\"center\" class='pages' >$prev $pagerstr $next</p>\n";
            $pagerbottom = "<p align=\"center\" class='pages' >$prev $pagerstr $next</p>\n";
        }
        else {
            $pagertop = "<p align=\"center\">$pager</p>\n";
            $pagerbottom = $pagertop;
        }

        $start = $page * $this->perpage;

        $this->limit = $start . "," . $this->perpage;
        $this->pager_top = $pagertop;
        $this->pager_bottom = $pagerbottom;
    }

}

?>
