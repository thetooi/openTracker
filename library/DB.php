<?php

class DB {

    protected $auto_free = false;
    protected $link_id = 0;
    protected $query_id = 0;
    public $record = array();
    protected $errno = 0;
    protected $error = "";
    protected $sort;
    protected $limit;
    protected $cols;
    protected $distinct = false;
    protected $valueData = array();
    protected $table;
    protected $query;
    protected $primaryKey;
    private $queryString;
    protected $join = array();
    protected $colPrefix;
    protected $db_name = "";
    protected $db_prefix = "";
    protected $configs = array();
    protected $db_type = "";

    public function __construct($table = null) {
        $fw = new Main;
        $fw->loadConfig("database.php");
        $fw->loadConfig("system.php");
        $this->configs = $fw->configs;
        $this->db_type = $this->configs['mysql']['type'];
        if ($this->connect()) {
            $this->selectDb($this->configs['mysql']['database']);
            if ($table != null) {
                $this->setTable($this->configs['mysql']['prefix'] . $table);
                return true;
            }
        }
    }

    public function connect() {
        if ($this->db_type == "mysqli") {
            $this->link_id = new mysqli($this->configs['mysql']['hostname'], $this->configs['mysql']['username'], $this->configs['mysql']['password']);
            $this->link_id->set_charset("utf8");
        } else if ($this->db_type == "mysql") {
            $this->link_id = mysql_connect($this->configs['mysql']['hostname'], $this->configs['mysql']['username'], $this->configs['mysql']['password']);
        }
        if (!$this->link_id) {
            $this->halt("False link id, DB connect failed");
        }
        return $this->link_id;
    }

    public function selectDb($db) {
        $this->table = $db;
        if ($this->db_type == "mysqli") {
            if (!$this->link_id->select_db($db)) {
                $this->halt("Can not use database " . $db);
            }
        } else if ($this->db_type == "mysql") {
            if (!mysql_select_db($db, $this->link_id)) {
                $this->halt("Can not use database " . $db);
            }
        }
    }

    public function dropTable() {
        $this->query = "DROP TABLE IF EXISTS ";
        $this->query .= $this->table;
        $this->query($this->query);
    }

    public function query($sql = null) {
        $sql = str_replace("{PREFIX}", $this->configs['mysql']['prefix'], $sql);
        if (empty($sql)) {
            return false;
        } if ($this->auto_free) {
            $this->freeResult();
        }

        if ($this->db_type == "mysqli") {
            $this->query_id = $this->link_id->query($sql);
            if ($this->link_id->error != "")
                $this->halt($this->link_id->error);
        } else if ($this->db_type == "mysql") {
            $this->query_id = mysql_query($sql, $this->link_id) or $this->halt(mysql_error());
        }
        if (!$this->query_id) {
            if ($this->db_type == "mysqli") {
                $this->errno = $this->link_id->errno;
                $this->error = $this->link_id->error;
            } else if ($this->db_type == "mysql") {
                $this->errno = mysql_errno($this->link_id);
                $this->error = mysql_error($this->link_id);
            }
            $this->halt("Invalid SQL: " . $sql);
        }

        return $this->query_id;
    }

    public function select($additionalStatement = "") {
        $this->query = "SELECT ";
        $where = preg_replace("/where/", "", $additionalStatement, 1);
        $this->query .= ( $this->distinct ) ? "DISTINCT " : "";
        $this->query .= ( isset($this->cols) ) ? implode(", ", $this->cols) . " \n" : "* ";
        $this->query .= "FROM " . $this->table . "\n";
        $this->query .= ( count($this->join > 0) ) ? implode("\n", $this->join) . "\n" : "";
        $this->query .= ( $additionalStatement != "") ? "WHERE " . $where . "\n" : "";
        $this->query .= ( is_array($this->sort) ) ? " ORDER BY " . implode(",", $this->sort) : "";
        $this->query .= ( isset($this->limit) ) ? " LIMIT " . $this->limit : "";
        $this->query($this->query);
    }

    public function insert() {
        $numCols = count($this->valueData);
        if ($numCols > 0) {
            $colNames = $this->getColNames($this->valueData);
            $colValues = array_map(array($this, "escape"), $this->valueData);
            $this->query = "INSERT INTO ";
            $this->query .= $this->table;
            $this->query .= " (";
            $this->query .= ( isset($this->colPrefix) ) ? $this->colPrefix : "";
            $this->query .= ( isset($this->colPrefix) ) ? implode(", " . $this->colPrefix, $colNames) : implode(", ", $colNames);
            $this->query .= ") VALUES ('";
            $this->query .= implode("', '", $colValues);
            $this->query .= "') ";
            $this->query($this->query);
        } else {
            trigger_error("No values set for insertion. Use \$db->column_name = \"value\";", E_USER_ERROR);
        }
    }

    public function update($whereStatement = "") {
        $numCols = count($this->valueData);
        $where = str_ireplace("where", "", $whereStatement);
        $valueData = array_map(array($this, "escape"), $this->valueData);
        if ($numCols > 0) {
            $this->query = "UPDATE ";
            $this->query .= $this->table;
            $this->query .= " SET\n";
            $c = 1;
            foreach ($valueData as $col => $val) {
                $this->query .= ( isset($this->colPrefix) ) ? $this->colPrefix . $col . " = '" . $val . "'" : $col . " = '" . $val . "'";
                $this->query .= ( $c < $numCols ) ? ",\n" : "";
                $c++;
            } $this->query .= "\n";
            if ($where != "") {
                if (strtolower($where) != "all") {
                    $this->query .= "WHERE " . $where;
                }
            } $this->query .= ( isset($this->limit) ) ? "\nLIMIT " . $this->limit : "";
            $this->query($this->query);
        } else {
//trigger_error("No values set for update. Use \$db->column_name = \"value\";", E_USER_ERROR);
        }
    }

    public function delete($whereStatement) {
        $where = str_ireplace("where", "", $whereStatement);
        $this->query = "DELETE FROM ";
        $this->query .= $this->table;
        $this->query .= " WHERE " . $where;
        $this->query($this->query);
    }

    private function getColNames($colArray) {
        $colNames = array();
        foreach ($colArray as $col => $val) {
            $colNames[] = $col;
        } return $colNames;
    }

    public function getQuery() {
        $this->getQuery = true;
    }

    public function setTable($tableName) {
        $this->table = $tableName;
    }

    public function setCols($cols, $prefix = null) {
        if (is_array($cols)) {
            $this->cols = $cols;
            if ($prefix != null) {
                $this->setColPrefix($prefix);
            }
        } else {
            $this->setCol($cols);
        }
    }

    public function setCol($col) {
        $this->cols[] = $col;
    }

    public function setColPrefix($prefix) {
        $this->colPrefix = $prefix;
        if (is_array($this->cols)) {
            foreach ($this->cols as $key => $value) {
                $this->cols[$key] = $prefix . $value;
            }
        }
        if (is_array($this->valueData)) {
            foreach ($this->valueData as $key => $value) {
                $this->valueData[$prefix . $key] = $value;
                unset($this->valueData[$key]);
            }
        }
    }

    public function setSort($sort) {
        $this->sort[] = $sort;
    }

    public function unsetSort() {
        $this->sort = array();
    }

    public function setOrder($sort) {
        $this->setSort($sort);
    }

    public function unsetOrder() {
        $this->unsetSort();
    }

    public function setOrderBy($sort) {
        $this->setSort($sort);
    }

    public function unsetOrderBy() {
        $this->unsetSort();
    }

    public function setDistinct() {
        $this->distinct = true;
    }

    public function setLimit($value) {
        $this->limit = $value;
    }

    public function unsetLimit() {
        $this->limit = null;
    }

    public function __get($col) {
        if (isset($this->colPrefix)) {
            return $this->f($this->colPrefix . $col);
        } else {
            return $this->f($col);
        }
    }

    public function __set($key, $val) {
        $this->valueData[$key] = $val;
    }

    public function join($type, $table, $tableAndColA, $tableAndColB) {
        $this->join[] = $this->getJoinType($type) . " " . $table . " ON " . $tableAndColA . " = " . $tableAndColB;
    }

    public function joinStatement($joinStatement) {
        $this->join[] = $joinStatement;
    }

    private function getJoinType($key) {
        $key = strtolower($key);
        switch ($key) {
            case "left": return "LEFT JOIN";
                break;
            case "inner": return "INNER JOIN";
                break;
            case "right": return "RIGHT JOIN";
                break;
        }
    }

    public function getId() {
        if ($this->db_type == "mysqli")
            return $this->link_id->insert_id;
        else if ($this->db_type == "mysql")
            return mysql_insert_id($this->link_id);
    }

    public function nextRecord($result_type = "both") {
        if (!$this->query_id) {
            $this->halt("next_record() called with no pending query.");
            return false;
        }
        if ($this->db_type == "mysqli") {
            switch ($result_type) {
                case "assoc":
                    $this->record = $this->query_id->fetch_assoc();
                    break;
                case "num":
                    $this->record = $this->query_id->fetch_fetch_row();
                    break;
                case "both":
                    $this->record = $this->query_id->fetch_array();
                    break;
            }
        } else if ($this->db_type == "mysql") {
            switch ($result_type) {
                case "assoc":
                    $this->record = mysql_fetch_assoc($this->query_id);
                    break;
                case "num":
                    $this->record = mysql_fetch_row($this->query_id);
                    break;
                case "both":
                    $this->record = mysql_fetch_array($this->query_id);
                    break;
            }
        }

        if ($this->db_type == "mysqli") {
            $this->errno = $this->link_id->errno;
            $this->error = $this->link_id->error;
        } else if ($this->db_type == "mysql") {
            $this->errno = mysql_errno($this->link_id);
            $this->error = mysql_error($this->link_id);
        }

        $status = is_array($this->record);
        if (!$status && $this->auto_free) {
            $this->freeResult();
        } return $status;
    }

    public function next_record($result_type = "both") {
        return $this->nextRecord($result_type);
    }

    public function escape($str) {
        if ($this->db_type == "mysqli")
            return $this->link_id->real_escape_string($str);
        else if ($this->db_type == "mysql")
            return mysql_escape_string($str);
    }

    public function escapeAll($str) {
        $str = str_replace("%", "", $str);
        if ($this->db_type == "mysqli")
            return $this->link_id->real_escape_string($str);
        else if ($this->db_type == "mysql")
            return mysql_escape_string($str);
    }

    public function f($name) {
        return $this->record[$name];
    }

    public function affectedRows() {
        if ($this->db_type == "mysqli")
            return $this->link_id->affected_rows;
        else if ($this->db_type == mysql)
            return mysql_affected_rows($this->link_id);
    }

    public function numRows() {
        if ($this->db_type == "mysqli")
            return $this->query_id->num_rows;
        else if ($this->db_type == "mysql")
            return mysql_num_rows($this->query_id);
    }

    public function numFields() {
        if ($this->db_type == "mysqli")
            return $this->link_id->field_count;
        else if ($this->db_type == "mysql")
            return mysql_num_fields($this->query_id);
    }

    public function fieldNames($table) {
        $arr_cols = array();
        $i = 0;
        $this->query("SHOW COLUMNS FROM " . $table);
        while ($this->next_record()) {
            $arr_cols[$i] = $this->f("0");
            $i++;
        } return $arr_cols;
    }

    public function tableNames() {
        $arr_tables = array();
        $i = 0;
        $this->query("SHOW TABLES FROM " . $this->db_name);
        while ($this->next_record()) {
            $arr_tables[$i] = $this->f("0");
            $i++;
        } return $arr_tables;
    }

    private function freeResult() {
        
    }

    private function halt($msg = null) {
        if (!$this->configs['system']['live']) {
            $error_message = null;
            $error_message .= "MYSQL_ERROR - " . $this->db_name . "<br />\n";
            $error_message .= "<b>Database error:</b> " . $msg . "<br />\n";
            $error_message .= "<b>MYSQL Error</b>: " . $this->errno . " (" . $this->error . ")<br />\n";
            echo $error_message;
        }
        die("This page is unavailable at the moment. Please try again.");
    }

}

?>